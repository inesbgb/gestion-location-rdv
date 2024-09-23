<?php

namespace App\Controller;

use App\Repository\ElementAdminRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'accueil_visiteur')]
    public function index(ElementAdminRepository $elementAdminRepository): Response
    {
        $elementAdmin = $elementAdminRepository->findOneBy([], ['id' => 'DESC']);

        return $this->render('home/index.html.twig', [
            'elementAdmin' => $elementAdmin,
        ]);
    }
}