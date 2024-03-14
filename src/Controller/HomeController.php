<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route(path: '/', name: 'home_')]
class HomeController extends AbstractController
{
    #[Route(path: '', name: 'home', methods: ['GET'])]
    public function home(EventRepository $eventRepository): Response
    {
        //on récupère l'utilisateur connecté
        $user = $this->getUser();

        return $this->render('home/home.html.twig', [
            //on récupère tous les events créés
            'events' => $eventRepository->findAll(),
        ]);
    }
}