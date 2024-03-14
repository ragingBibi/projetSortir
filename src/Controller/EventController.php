<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\StatusRepository;
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

    #[Route('/create', name: 'create', methods: ['POST'])]
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

}
