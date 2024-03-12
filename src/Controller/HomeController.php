<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route(path: '/', name: 'home_')]
class HomeController extends AbstractController
{
    #[Route(path: '', name: 'home', methods: ['GET'])]
    public function home(EventRepository $eventRepository): Response
    {
        return $this->render('home/home.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

}