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
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route(path: '/', name: 'home_')]
#[IsGranted('ROLE_USER')]
class HomeController extends AbstractController
{
    private Security $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route(path: '', name: 'home', methods: ['GET'])]
    public function home(EventRepository $eventRepository, Request $request): Response
    {
        if($this->isGranted("ROLE_USER")) {

            //On récupère l'utilisateur connecté
            $user = new User();
            $user = $this->security->getUser();
            //On crée le formulaire de recherche
            $searchData = new SearchData();
            $form = $this->createForm(EventSearchType::class, $searchData);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $events = $eventRepository->findByFilters($searchData, $user);
            } else {
                $events = $eventRepository->findAll();

            }
            return $this->render('home/home.html.twig', [
                'events' => $events,
                'form' => $form->createView()
            ]);
        }
        else{
            return $this->redirectToRoute('app_login');
        }
    }

    public function getSecurity(): Security
    {
        return $this->security;
    }

    public function setSecurity(Security $security): HomeController
    {
        $this->security = $security;
        return $this;
    }

}