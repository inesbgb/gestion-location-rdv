<?php

namespace App\Controller;

use App\DTO\SearchData;
use App\Entity\Produit;
use App\Form\SearchType;
use App\Form\ProduitType;
use App\Entity\Reservation;
use App\Repository\TailleRepository;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
#[Route('/produit/admin')]
class ProduitAdminController extends AbstractController
{
    #[Route('/', name: 'app_produit_admin_index', methods: ['GET'])]
    public function index(Request $request, ProduitRepository $produitRepository): Response
    {
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);

        $produits = $produitRepository->findSearch($searchData);

      
        $dateDebut = $searchData->dateDebut;
        $dateFin = $searchData->dateFin;

        // Calculer la disponibilité pour chaque produit
        $disponibilites = [];
        if ($dateDebut && $dateFin) {
            foreach ($produits as $produit) {
                $disponibilites[$produit->getId()] = $produitRepository->isProduitDisponible($produit, $dateDebut, $dateFin);
            }
        }

        return $this->render('produit_admin/index.html.twig', [
            'produits' => $produits,
            'form' => $form->createView(),
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'disponibilites' => $disponibilites,
        ]);
    }
    #[Route('/disponibilite', name: 'app_produit_admin_disponibilite', methods: ['GET'])]
    public function getDisponibilite(Request $request, ProduitRepository $produitRepository): JsonResponse
    {
        $dateDebut = new \DateTime($request->query->get('dateDebut'));
        $dateFin = new \DateTime($request->query->get('dateFin'));
    
        $produits = $produitRepository->findAll();
        $produitIds = array_map(fn($p) => $p->getId(), $produits);
    
        $disponibilites = $produitRepository->getDisponibilites($produitIds, $dateDebut, $dateFin);
    
        return new JsonResponse($disponibilites);
    }

    #[Route('/{id}/toggle-disponibilite', name: 'app_produit_admin_toggle_disponibilite', methods: ['POST'])]
    public function toggleDisponibilite(Request $request, Produit $produit, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $reservationId = $data['reservationId'] ?? null;

        if (!$reservationId) {
            return new JsonResponse(['success' => false, 'message' => 'ID de réservation manquant'], 400);
        }

        $reservation = $entityManager->getRepository(Reservation::class)->find($reservationId);
        if (!$reservation) {
            return new JsonResponse(['success' => false, 'message' => 'Réservation non trouvée'], 404);
        }

        $reservation->setRetour(true);
        $produit->setStock(true);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
    #[Route('/new', name: 'app_produit_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit_admin/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_admin_show', methods: ['GET'])]
public function show(Produit $produit): Response
{
    return $this->render('produit_admin/show.html.twig', [
        'produit' => $produit,
        'reservations' => $produit->getReservations(), 
    ]);
}

    #[Route('/{id}/edit', name: 'app_produit_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit_admin/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
