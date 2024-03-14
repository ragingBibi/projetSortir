<?php

namespace App\Controller;

use App\Entity\Venue;
use App\Form\VenueType;
use App\Repository\VenueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/venue', name: 'app_venue_')]
#[IsGranted('ROLE_ADMIN')]
class VenueController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(VenueRepository $venueRepository): Response
    {
        return $this->render('venue/index.html.twig', [
            'venues' => $venueRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $venue = new Venue();
        $form = $this->createForm(VenueType::class, $venue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($venue);
            $entityManager->flush();

            return $this->redirectToRoute('app_venue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('venue/new.html.twig', [
            'venue' => $venue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Venue $venue): Response
    {
        return $this->render('venue/show.html.twig', [
            'venue' => $venue,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Venue $venue, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VenueType::class, $venue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_venue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('venue/edit.html.twig', [
            'venue' => $venue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Venue $venue, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$venue->getId(), $request->request->get('_token'))) {
            $entityManager->remove($venue);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_venue_index', [], Response::HTTP_SEE_OTHER);
    }
}
