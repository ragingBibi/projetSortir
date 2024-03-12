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

            // TODO : Validation de la date de début de l'évennement -> minimum now
            // TODO : Validation de la date limite d'inscripttion -> maximum date de l'évennement
            // TODO : Champs obligatoires

            // Associer USER au Creator de l'event
            $event->setCreator($this->getUser());

            // Status par défaut lors de la création -> Créé
            $event->setStatus($statusRepository->findOneBy(['label' => 'Créé']));

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Evenement enregistré');
            return $this->redirectToRoute('home_home');
        }

        return $this->render('home/create.html.twig', [
            'create_event_form' => $form
        ]);
    }


//    #[Route('/{id}', name: 'detail', methods: ['GET'])]
//    public function detail(Event $event): Response
//    {
//        return $this->render('home/detail.html.twig', [
//            'event' => $event,
//        ]);
//    }
//
//    #[Route('/{id}/modifier', name: 'update', methods: ['GET', 'POST'])]
//    public function update(Request $request, Event $event, EntityManagerInterface $entityManager): Response
//    {
//        $form = $this->createForm(EventType::class, $event);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('event/edit.html.twig', [
//            'event' => $event,
//            'form' => $form,
//        ]);
//    }
//
//    #[Route('/{id}', name: 'delete', methods: ['POST'])]
//    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
//            $entityManager->remove($event);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
//    }
}
