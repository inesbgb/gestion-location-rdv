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
{  #[Route('/', name: 'app_admin')]
    public function index(
        RendezVousRepository $rdvRepo,
        ClientRepository $clientRepo,
        ReservationRepository $reservationRepo
    ): Response {
        $chartData = [
            'labels' => [],
            'revenues' => [],
            'reservations' => []
        ];
        
        // Récupérez les données des 12 derniers mois
        for ($i = 11; $i >= 0; $i--) {
            $date = new \DateTime("-$i months");
            $monthStart = $date->format('Y-m-01');
            $monthEnd = $date->format('Y-m-t');
            
            $chartData['labels'][] = $date->format('M Y');
            $chartData['revenues'][] = $reservationRepo->getRevenueForPeriod($monthStart, $monthEnd);
            $chartData['reservations'][] = $reservationRepo->getReservationsCountForPeriod($monthStart, $monthEnd);
        }

        return $this->render('admin/index.html.twig', [
            'rdvCount' => $rdvRepo->countTodayRdv(),
            'rdvs' => $rdvRepo->findTodayRdv(),
            'newClientsThisMonth' => $clientRepo->countNewClientsThisMonth(),
            'monthlyRdvCount' => $rdvRepo->countMonthlyRdv(),
            'monthlyReservationsCount' => $reservationRepo->countMonthlyReservations(),
            'monthlyRevenue' => $reservationRepo->getMonthlyRevenue(),
            'chartData' => $chartData,
        ]);
    }
}