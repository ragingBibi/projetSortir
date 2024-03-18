<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventSearchType;
use App\Model\SearchData;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use DateTime;
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
        $searchData = new SearchData();
        $form = $this->createForm(EventSearchType::class, $searchData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $events = $eventRepository->findBySearch($searchData);
            dd($events);

        } else {
            $events = $eventRepository->findAll();

        }
        return $this->render('home/home.html.twig', [
            'events' => $events,
            'form' => $form->createView()
        ]);

    }
}