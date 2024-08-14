<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produit')]
class ProduitController extends AbstractController
{
    // #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    // public function index(ProduitRepository $produitRepository): Response
    // {
    //     return $this->render('produit/index.html.twig', [
    //         'produits' => $produitRepository->findAll(),
    //     ]);
    // }
    #[Route('/location', name: 'app_produit_location', methods: ['GET'])]
public function produitsLocation(ProduitRepository $produitRepository): Response // a changer

{
    $produitsLocation = $produitRepository->findBy(['liquidation' => false]);

    return $this->render('produit/location.html.twig', [
        'produits' => $produitsLocation,
    ]);
}

    #[Route('/liquidation', name: 'app_produit_liquidation', methods: ['GET'])]
public function produitsLiquidation(ProduitRepository $produitRepository): Response
{
    $produitsEnLiquidation = $produitRepository->findBy(['liquidation' => true]);

    return $this->render('produit/liquidation.html.twig', [
        'produits' => $produitsEnLiquidation,
    ]);
}
    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

   

   
}
