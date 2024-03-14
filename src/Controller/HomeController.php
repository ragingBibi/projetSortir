<?php

namespace App\Controller;

use App\Entity\EventSearch;
use App\Entity\User;
use App\Form\EventSearchType;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route(path: '/', name: 'home_')]
class HomeController extends AbstractController
{
    #[Route(path: '', name: 'home', methods: ['GET'])]
    public function home(EventRepository $eventRepository, Request $request): Response
    {
        //on récupère l'utilisateur connecté
        $user = $this->getUser();
        //On crée le formulaire de recherche
        $search = new EventSearch();
        $form = $this->createForm(EventSearchType::class, $search);
        $form->handleRequest($request);

        return $this->render('home/home.html.twig', [
            //on récupeère tous les events créés
            'events' => $eventRepository->findAll(),
            //On passe le formulaire de recherche à la vue
            'form' => $form->createView()
        ]);
    }
}