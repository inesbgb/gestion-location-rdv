<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DistanceController extends AbstractController
{
    #[Route('/distance', name: 'app_distance')]
    public function index(): Response
    {
        return $this->render('distance/index.html.twig', [
            'controller_name' => 'DistanceController',
        ]);
    }
}
