<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
public function produitsLocation(ProduitRepository $produitRepository, PaginatorInterface $paginator, Request $request): Response
{
    $query = $produitRepository->createQueryBuilder('p')
        ->where('p.liquidation = :liquidation')
        ->setParameter('liquidation', false)
        ->groupBy('p.designation')  // Groupe par désignation
        ->getQuery();

    $produits = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        12 
    );

    return $this->render('produit/location.html.twig', [
        'produits' => $produits,
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
public function show(Produit $produit, ProduitRepository $produitRepository): Response
{
    // Récupère tous les produits avec la même désignation
    $produitsMemeTaille = $produitRepository->findBy(['designation' => $produit->getDesignation()]);

    return $this->render('produit/show.html.twig', [
        'produit' => $produit,
        'produitsMemeTaille' => $produitsMemeTaille,
    ]);
}

   

   
}
