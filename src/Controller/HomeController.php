<?php

namespace App\Controller;


use App\Repository\AvisClientRepository;
use App\Repository\ElementAdminRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'accueil_visiteur')]
    public function index(ElementAdminRepository $elementAdminRepository, AvisClientRepository $avisClientRepository): Response
{
    $elementAdmin = $elementAdminRepository->findOneBy([], ['id' => 'DESC']);
    $avisClients = $avisClientRepository->findAll();
    return $this->render('home/index.html.twig', [
        'elementAdmin' => $elementAdmin,
        'avisClients' => $avisClients,
    ]);
    }
}