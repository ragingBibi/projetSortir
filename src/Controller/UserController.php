<?php

namespace App\Controller;

use App\Entity\Status;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route(path: '/user', name: 'app_user_')]
class UserController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    //faire une fonction pour afficher un utilisateur

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): Response
    {
        $organizedEvents = $user->getOrganizedEvents();

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'organizedEvents' => $organizedEvents
        ]);
    }

    #[Route('/profile/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function updateUser(User $user, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        if (!$user->isIsActive()) {
            $this->addFlash('danger text-center', 'Ce compte a été désactivé par les administrateurs.');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            if($form->get('picture_file')->getData() instanceof UploadedFile) {
                $pictureFile = $form->get('picture_file')->getData();
                $fileName = $slugger->slug($user->getLastName()) . '-' . uniqid() . '.' . $pictureFile->guessExtension();
                $pictureFile->move('uploads', $fileName);
                $user->setPicture($fileName);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success text-center', 'Le profil a bien été mis à jour');
            return $this->redirectToRoute('app_user_edit', ['id' => $user->getId()]);
        }

        $organizedEvents = $user->getOrganizedEvents();

        return $this->render('user/edit.html.twig', [
            'form' => $form,
            'user' => $user,
            'organizedEvents' => $organizedEvents
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET', 'POST'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
//        $user->setIsActive(false);

        $currentDateTime = new \DateTime();

        // Désinscrire de tous les évènements futurs
        $futureAttendingEvents = $user->getAttendingEventsList()->filter(function($event) use ($currentDateTime) {
            return $event->getStartingDateTime() > $currentDateTime;
        });
        foreach ($futureAttendingEvents as $event) {
            $user->removeAttendingEventsList($event);
            $event->removeUserFromAttendeesList($user);
        }

        // Supprimer les évennements futurs que l'utilisateur a créé
        $futureOrganizedEvents = $user->getOrganizedEvents()->filter(function($event) use ($currentDateTime) {
            return $event->getStartingDateTime() > $currentDateTime;
        });
        foreach ($futureOrganizedEvents as $event) {
            $entityManager->remove($event);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success text-center', 'L\'utilisateur a été supprimé');
        return $this->redirectToRoute('home_home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/disable', name: 'disable', methods: ['Get', 'POST'])]
    public function disable(User $user, EntityManagerInterface $entityManager): Response {

        $user->setIsActive(false);

        $currentDateTime = new \DateTime();

        // Récupérer tous les événements futurs auxquels l'utilisateur est inscrit
        $futureEvents = $user->getAttendingEventsList()->filter(function($event) use ($currentDateTime) {
            return $event->getStartingDateTime() > $currentDateTime;
        });

        // Désinscrire l'utilisateur de ces événements
        foreach ($futureEvents as $event) {
            $user->removeAttendingEventsList($event);
            $event->removeUserFromAttendeesList($user);
        }

        // Annuler les évennements futurs que l'utilisateur a créé
        $futureOrganizedEvents = $user->getOrganizedEvents()->filter(function($event) use ($currentDateTime) {
            return $event->getStartingDateTime() > $currentDateTime;
        });
        foreach ($futureOrganizedEvents as $event) {
            $event->setStatus($entityManager->getRepository(Status::class)->findOneBy(['label' => 'Annulé']));
            $event->setCancellationDate(new \DateTimeImmutable());
            $event->setCancellationReason('Le compte de cet utilisateur a été désactivé');

            $entityManager->persist($event);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success text-center', 'L\'utilisateur a été désactivé');
        return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
    }

    // Fonction pour désactiver un utilisateur
    #[Route('/{id}/activate', name: 'activate', methods: ['Get', 'POST'])]
    public function activate(User $user, EntityManagerInterface $entityManager): Response {

        $user->setIsActive(true);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success text-center', 'L\'utilisateur a été réactivé');
        return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
    }

}
