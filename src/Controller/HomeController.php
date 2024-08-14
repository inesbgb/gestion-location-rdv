<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'accueil_visiteur')]
    public function index(): Response
    {
        // Définir la variable is_admin pour les visiteurs
        $isAdmin = false;

        return $this->render('home/index.html.twig', [
            'is_admin' => $isAdmin,
        ]);
    }

   
}
