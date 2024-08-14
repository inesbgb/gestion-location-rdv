<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\RendezVousRepository;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Controller\AdminBaseController; // Assurez-vous que cette ligne est correcte

#[Route('/admin')]
class AdminController extends AdminBaseController
{
     #[Route('/', name: 'app_admin')]
     public function index(
        RendezVousRepository $rdvRepo, 
        ClientRepository $clientRepo, 
        ReservationRepository $reservationRepo
    ): Response
    {
        return $this->render('admin/index.html.twig', [
            'rdvCount' => $rdvRepo->countTodayRdv(),
            'rdvs' => $rdvRepo->findTodayRdv(),
            'newClientsThisMonth' => $clientRepo->countNewClientsThisMonth(),
            'monthlyRdvCount' => $rdvRepo->countMonthlyRdv(),
            'monthlyReservationsCount' => $reservationRepo->countMonthlyReservations(),
            'monthlyRevenue' => $reservationRepo->getMonthlyRevenue(),
        ]);
    }
    // Autres méthodes...
}