<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Form\RendezVousType;
use App\Service\EmailService;
use App\Repository\JourRepository;
use App\Form\AnnulezRendezVousType;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppointmentController extends AbstractController
{
    #[Route('/rendezvous', name: 'app_appointment')]
    public function index(Request $request, EntityManagerInterface $entityManager,EmailService $emailService, RendezVousRepository $rendezVousRepository, JourRepository $jourRepo): Response
    {
        $rendezVous = new RendezVous();
        $form = $this->createForm(RendezVousType::class, $rendezVous);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
         
            $date = $rendezVous->getDateRdv();
            $time = $rendezVous->getHeureRdv();
            $dateTime = \DateTime::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $time->format('H:i'));
            $rendezVous->setDateRdv($dateTime);

        // Vérifier la disponibilité
        $unavailableSlots = $rendezVousRepository->findUnavailableSlots($dateTime);
        if (!empty($unavailableSlots)) {
            $this->addFlash('error', 'Ce créneau horaire est déjà réservé.');
            return $this->redirectToRoute('app_appointment');
        }

            // Définir le statut à "confirmé" (true)
            $rendezVous->setStatut(true);

            // Générer un numéro de rendez-vous unique
            $rendezVous->setNumRdv($this->generateUniqueNumRdv($entityManager));
            $entityManager->persist($rendezVous);
            $entityManager->flush();

            // Envoyer l'email de confirmation
            $emailService->sendConfirmationEmail(
                $rendezVous->getEmail(), // pour récupéré l'email de l'utilisateur afin d'envoyer le mail
                'Confirmation de votre rendez-vous',
                $this->renderView('emails/confirmation.html.twig', ['rendezVous' => $rendezVous])
            );

            return $this->redirectToRoute('rendezvous_success');
        }

        // Récupérer les rendez-vous existants
        $existingAppointments = $entityManager->getRepository(RendezVous::class)->findAll();
        $openDaysEntities = $jourRepo->findOpenDays();
        $openDays = [];
        foreach ($openDaysEntities as $jour) {
            $openDays [] = $jour->getLibelle();
        }
    



        return $this->render('appointment/index.html.twig', [
            'form' => $form->createView(),
            'existingAppointments' => $existingAppointments,
            "openDays"=>$openDays
        ]);
    }
    

    #[Route('/rendezvous/success', name: 'rendezvous_success')]
    public function success(): Response
    {
        return $this->render('appointment/success.html.twig');
    }

    private function generateUniqueNumRdv(EntityManagerInterface $entityManager): int
    {
        do {
            $numRdv = random_int(100000, 999999);
            $existingRdv = $entityManager->getRepository(RendezVous::class)->findOneBy(['num_rdv' => $numRdv]);
        } while ($existingRdv !== null);

        return $numRdv;
    }
    #[Route('/rendezvous/annuler', name: 'rendezvous_annuler', methods: ['GET', 'POST'])]
public function annuler(Request $request, EntityManagerInterface $entityManager, EmailService $emailService): Response
{
    $form = $this->createForm(AnnulezRendezVousType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $numRdv = $form->get('numRdv')->getData();
        $rendezvous = $entityManager->getRepository(RendezVous::class)->findOneBy(['num_rdv' => $numRdv]);

        if ($rendezvous) {
            $now = new \DateTime();
            $rdvDate = $rendezvous->getDateRdv();
            $interval = $now->diff($rdvDate);
            
            if ($interval->days >= 1 || ($interval->days == 0 && $interval->h >= 24)) {
                $rendezvous->setStatut(false);
                $entityManager->flush();

                // Envoyer l'email de confirmation
                $emailService->sendConfirmationEmail(
                    $rendezvous->getEmail(),
                    'Confirmation d\'annulation de rendez-vous',
                    $this->renderView('emails/cancel_confirmation.html.twig', ['rendezvous' => $rendezvous])
                );

                $this->addFlash('success', 'Le rendez-vous a été annulé et un email de confirmation a été envoyé.');
            } else {
                $this->addFlash('error', 'Désolé, vous ne pouvez plus annuler ce rendez-vous sur notre site. 
                L\'annulation doit être faite au moins 24 heures à l\'avance. Contactez nous sur whatsapp');
            }
        } else {
            $this->addFlash('error', 'Numéro de rendez-vous invalide.');
        }

        return $this->redirectToRoute('app_appointment');
    }

    return $this->render('appointment/cancel_rdv.html.twig', [
        'form' => $form->createView(),
    ]);
}

    
   
#[Route('/check-availability', name: 'check_availability')]
public function checkAvailability(Request $request, RendezVousRepository $rendezVousRepository): JsonResponse
{
    $dateStr = $request->query->get('date');
    $date = new \DateTime($dateStr);

    $takenSlots = $rendezVousRepository->findTakenSlots($date);

    $allSlots = ['10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];

    $takenTimes = array_map(function($slot) {
        if ($slot['heure_rdv'] instanceof \DateTimeInterface) {
            return $slot['heure_rdv']->format('H:i');
        } elseif (is_string($slot['heure_rdv'])) {
           
            return substr($slot['heure_rdv'], 0, 5);
        }
       
        return '00:00';
    }, $takenSlots);

    $availableSlots = array_diff($allSlots, $takenTimes);

   
    return new JsonResponse(array_values($availableSlots));
}

}