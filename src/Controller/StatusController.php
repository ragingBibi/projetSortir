<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventDeleteType;
use App\Form\EventFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Status;

#[Route(path: '/event', name: 'status_')]
class StatusController extends AbstractController
{

    #[\Symfony\Component\Routing\Attribute\Route('/{id}/publish', name: 'publish', methods: ['GET', 'POST'])]
    public function publish(Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($event->getStatus()->getLabel() == 'Créé' or $event->getStatus()->getLabel() == 'Annulé') {
            $event->setStatus($entityManager->getRepository(Status::class)->findOneBy(['label' => 'Ouvert']));

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'L\'évènement est publié');
        }

        return $this->redirectToRoute('event_details', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);
    }

    #[\Symfony\Component\Routing\Attribute\Route('/{id}/delete', name: 'delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventDeleteType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($event->getStatus()->getLabel() != 'Annulé') {
                $event->setStatus($entityManager->getRepository(Status::class)->findOneBy(['label' => 'Annulé']));

                $event->setCancellationDate(new \DateTimeImmutable());

                $entityManager->persist($event);
                $entityManager->flush();

                $this->addFlash('success', 'L\'évènement est annulé');
                return $this->redirectToRoute('event_details', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('event/delete.html.twig', [
            'event' => $event,
            'delete_event_form' => $form
        ]);
    }

//    public function archive(Request $request, Event $event, EntityManagerInterface $entityManager): Response
//    {
//
//    }

}