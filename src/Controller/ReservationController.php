<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Client;
use App\Entity\Produit;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Service\EmailService;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationController extends AbstractController
{
    #[Route('/produit/{id}/reserver', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        Produit $produit,
        EntityManagerInterface $entityManager,
        ClientRepository $clientRepository,
        EmailService $emailService
    ): Response {
        $reservation = new Reservation();
        $reservation->setProduit($produit);
        
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $clientEmail = $form->get('clientEmail')->getData();
            $client = $clientRepository->findOneBy(['email' => $clientEmail]);
    
            if (!$client) {
                $client = new Client();
                $client->setEmail($clientEmail);
                $entityManager->persist($client);
            }
    
            $client->setPrenom($form->get('clientPrenom')->getData());
            $client->setNom($form->get('clientNom')->getData());
            $client->setTelephone($form->get('clientTelephone')->getData());
    
            $reservation->setClient($client);
    
            // Gestion du dépôt
            $depotType = $form->get('depotType')->getData();
            $depotMontant = $form->get('depotMontant')->getData();
            if ($depotType && $depotMontant) {
                $depot = new Depot();
                $depot->setType($depotType);
                $depot->setMontant($depotMontant);
                $reservation->setDepot($depot);
                $entityManager->persist($depot);
            }
    
            $entityManager->persist($reservation);
            $entityManager->flush();
    
            // Envoi de l'e-mail de confirmation
            $this->sendConfirmationEmail($emailService, $reservation);
    
            $this->addFlash('success', 'Réservation créée avec succès et un e-mail de confirmation a été envoyé au client.');
            return $this->redirectToRoute('app_produit_admin_show', ['id' => $produit->getId()]);
        }
    
       
    
        return $this->render('produit_admin/reservation.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
            
        ]);
    }

    private function sendConfirmationEmail(EmailService $emailService, Reservation $reservation): void
    {
        $emailContent = $this->renderView('emails/reservation_confirmation.html.twig', [
            'reservation' => $reservation,
        ]);

        $emailService->sendEmail(
            $reservation->getClient()->getEmail(),
            'Confirmation de votre réservation',
            $emailContent
        );
    }

    
    
    #[Route('/reservation/{id}/return', name: 'app_reservation_return', methods: ['POST'])]
    public function returnReservation(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('return'.$reservation->getId(), $request->request->get('_token'))) {
            $reservation->setRetour(true);
            $entityManager->flush();

            $this->addFlash('success', 'La réservation a été marquée comme retournée.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_produit_admin_show', ['id' => $reservation->getProduit()->getId()]);
    }

}