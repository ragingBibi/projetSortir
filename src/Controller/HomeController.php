<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


#[Route(path: '/', name: 'home_')]
class HomeController extends AbstractController
{
    #[Route(path: '', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('home/home.html.twig');
    }

}