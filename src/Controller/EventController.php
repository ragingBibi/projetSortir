<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(name: 'event_')]
//#[IsGranted('ROLE_USER')]
class EventController extends AbstractController
{
    #[Route('/create', name:'create')]
//    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $em,): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Un livre a été enregistré');
            return $this->redirectToRoute('home_home');
        }

        return $this->render('home/create.html.twig', [
            'create_event_form' => $form
        ]);
    }

}