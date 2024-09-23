<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\RendezVousRepository;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Controller\AdminBaseController;

#[Route('/admin')]
class AdminController extends AdminBaseController
{
    #[Route('/', name: 'app_admin')]
    public function index(
        RendezVousRepository $rdvRepo,
        ClientRepository $clientRepo,
        ReservationRepository $reservationRepo
    ): Response {
        $currentMonth = new \DateTime('first day of this month');
        $monthStart = $currentMonth->format('Y-m-01');
        $monthEnd = $currentMonth->format('Y-m-t');

        $chartData = [
            'labels' => [$currentMonth->format('M Y')],
            'revenues' => [$reservationRepo->getRevenueForPeriod($monthStart, $monthEnd)],
            'reservations' => [$reservationRepo->getReservationsCountForPeriod($monthStart, $monthEnd)]
        ];

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