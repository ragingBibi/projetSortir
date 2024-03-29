<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Status;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use App\Repository\StatusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/event', name: 'event_')]
#[IsGranted('ROLE_USER')]
class EventController extends AbstractController
{

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, StatusRepository $statusRepository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Associer USER au Creator de l'event
            $event->setCreator($this->getUser());

            // Status par défaut lors de la création -> Créé
            $event->setStatus($statusRepository->findOneBy(['label' => 'Créé']));

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'L\'évènement a été enregistré');
            return $this->redirectToRoute('event_details', ['id' => $event->getId()]);
        }

        return $this->render('event/create.html.twig', [
            'create_event_form' => $form
        ]);
    }


    #[Route('/{id}', name: 'details', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(Event $event): Response
    {
        $attendees = $event->getAttendeesList();

        if ($event->getStatus()->getLabel() == 'Annulé') {
           $this->addFlash('warning', 'Cet évènement est annulé');
        }

        return $this->render('event/details.html.twig', [
            'event' => $event,
            'attendees' => $attendees
        ]);
    }

    #[Route('/{id}/modifier', name: 'update', methods: ['GET', 'POST'])]
    public function updateEvent(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'L\'évènement a été modifié');
            return $this->redirectToRoute('event_details', ['id' => $event->getId()]);
        }

        return $this->render('event/update.html.twig', [
            'event' => $event,
            'update_event_form' => $form,
        ]);
    }

    //s'inscrire à un évenement par pression d'un bouton, ajout de l'utilisateur dans la liste des participants event.attendeesList
    #[Route('/{id}/subscribe', name: 'subscribe', methods: ['POST', 'GET'])]
    public function subscribe(Event $event, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        //on récupère l'utilisateur connecté
        $userConnected = $this->getUser();
        //on recherche via le repository l'utilisateur qui correspond à l'email de l'utilisateur connecté
        // et on le stocke dans $user pour avoir un objet de type User
        $user = $userRepository->findOneBy(['email' => $userConnected->getEmail()]);


        //on pose la condition que la date de cloture des inscriptions à l'event n'est pas dépassée
       /* if (!$event->getRegistrationDeadline() > new \DateTime('now')) {*/

            //on ajoute l'utilisateur dans la liste des participants SI il reste de la place dans le nombre d'inscrits maximum
            if (count($event->getAttendeesList()) < $event->getMaxAttendees()) {
                $event->addUserToAttendeesList($user);
                $entityManager->persist($event);
                $entityManager->flush();
                //message flash
                $this->addFlash('success', 'Vous êtes inscrit à l\'évènement');
            } else {
                //message flash pour avertir de l'echec. Le user n'est pas inscrit car plus de place
                $this->addFlash('warning', 'Le nombre maximum d\'inscrits a été atteint, vous ne pouvez plus vous inscrire.');
            }
            //condition si la date de côture des inscriptions à l'event est dépassée
      /*  } else {
            //message flash pour avertir de l'echec. Le user n'est pas inscrit
            $this->addFlash('warning', 'La date de cloture de l\'évènement est dépassée, vous ne pouvez plus vous inscrire.');
        }*/

        //on affiche dans le détail de l'évènement qui détaille la liste des participants
        return $this->redirectToRoute('event_details', ['id' => $event->getId()]);
    }

    //se désinscire à un évenement par pression d'un bouton, enlève l'utilisateur de la liste des participants event.attendeesList
    #[Route('/{id}/unsubscribe', name: 'unsubscribe', methods: ['POST', 'GET'])]
    public function unsubcribe(Event $event, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        //on récupère l'utilisateur connecté
        $userConnected = $this->getUser();
        //on recherche via le repository l'utilisateur qui correspond à l'email de l'utilisateur connecté
        // et on le stocke dans $user pour avoir un objet de type User
        $user = $userRepository->findOneBy(['email' => $userConnected->getEmail()]);
        //on enlémentine l'utilisateur de la liste des participants
        $event->removeUserFromAttendeesList($user);
        $entityManager->persist($event);
        $entityManager->flush();

        //message flash
        $this->addFlash('success', 'Vous êtes désinscrit de l\'évènement');
        //on affiche dans le détail de l'évènement qui détaille la liste des participants
        return $this->redirectToRoute('event_details', ['id' => $event->getId()]);

    }

}
