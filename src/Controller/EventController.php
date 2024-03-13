<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Status;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/event', name: 'event_')]
//#[IsGranted('ROLE_USER')]
class EventController extends AbstractController
{

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, StatusRepository $statusRepository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // TODO : Validation de la date limite d'inscripttion -> maximum date de l'évennement
            // TODO : Champs obligatoires

            // Associer USER au Creator de l'event
            $event->setCreator($this->getUser());

            // Status par défaut lors de la création -> Créé
            $event->setStatus($statusRepository->findOneBy(['label' => 'Créé']));

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Evenement enregistré');
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

        return $this->render('event/details.html.twig', [
            'event' => $event,
            'attendees' => $attendees
        ]);
    }

    #[Route('/{id}/modifier', name: 'update', methods: ['GET', 'POST'])]
    public function update(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('event_details', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/update.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('home_home', [], Response::HTTP_SEE_OTHER);
    }
}
