<?php

namespace App\Controller;

use App\Entity\Horaire;
use App\Entity\RendezVous;
use App\Form\RendezVousType;
use App\Service\EmailService;
use App\Repository\JourRepository;
use App\Form\AnnulezRendezVousType;
use App\Repository\HoraireRepository;
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
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        EmailService $emailService,
        RendezVousRepository $rendezVousRepository,
        HoraireRepository $horaireRepository // Assurez-vous d'avoir un repository pour Horaire
    ): Response {
        $rendezVous = new RendezVous();

        // Récupérer la date de la requête, par exemple via un paramètre GET
        $dateParam = $request->query->get('date');
        if ($dateParam) {
            $dateRdv = \DateTime::createFromFormat('Y-m-d', $dateParam);
            if ($dateRdv === false) {
                throw new \InvalidArgumentException('La date fournie est invalide.');
            }
        } else {
            // Utiliser la date actuelle si aucune date n'est fournie
            $dateRdv = new \DateTime();
        }
        $rendezVous->setDateRdv($dateRdv);

        // Récupérer tous les créneaux horaires possibles depuis la table Horaire
        $allSlots = $horaireRepository->findAll();

        // Convertir les objets Horaire en un tableau de chaînes de créneaux horaires
        $allSlotTimes = array_map(function ($horaire) {
            return $horaire->getSlot()->format('H:i:s');
        }, $allSlots);

        // Utiliser la méthode du repository pour obtenir les créneaux disponibles
        $availableSlots = $rendezVousRepository->findAvailableSlots($dateRdv, $allSlotTimes);
        
       

        // Créer le formulaire avec les créneaux disponibles
        $form = $this->createForm(RendezVousType::class, $rendezVous, [
            'available_slots' => $availableSlots
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la date et l'heure sélectionnées
            $date = $rendezVous->getDateRdv();
            $time = $form->get('heure_rdv')->getData(); // Assurez-vous que c'est une chaîne

            // Vérifier que $date est un objet DateTime et que $time est une chaîne
            if ($date instanceof \DateTime && is_string($time)) {
                // Créer un objet DateTime pour l'heure
                $timeObject = \DateTime::createFromFormat('H:i:s', $time);

                if ($timeObject !== false) {
                    $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' ' . $time);

                    if ($dateTime !== false) {
                        $rendezVous->setDateRdv($dateTime);
                        $rendezVous->setHeureRdv($timeObject); // Assurez-vous que l'heure est bien assignée

                        // Vérifier si l'heure choisie est disponible
                        $availableSlots = $rendezVousRepository->findAvailableSlots($date, $allSlotTimes);
                        if (!in_array($time, $availableSlots)) {
                            $this->addFlash('error', 'Le créneau horaire choisi est déjà pris. Veuillez en sélectionner un autre.');
                            return $this->redirectToRoute('app_appointment');
                        }


                        // Définir le statut à "confirmé" et générer un numéro unique
                        $rendezVous->setStatut(true);
                        $rendezVous->setNumRdv($this->generateUniqueNumRdv($entityManager));

                        // Enregistrer le rendez-vous
                        $entityManager->persist($rendezVous);
                        $entityManager->flush();

                        // Envoyer l'email de confirmation
                        $emailService->sendConfirmationEmail(
                            $rendezVous->getEmail(),
                            'Confirmation de votre rendez-vous',
                            $this->renderView('emails/confirmation.html.twig', ['rendezVous' => $rendezVous])
                        );

                        return $this->redirectToRoute('rendezvous_success');
                    } else {
                        $this->addFlash('error', 'Erreur lors de la création de la date et de l\'heure.');
                    }
                } else {
                    $this->addFlash('error', 'Erreur lors de la création de l\'heure.');
                }
            } else {
                $this->addFlash('error', 'Date ou heure invalide.');
            }
        }

        // Exemple de jours ouverts
        $openDays = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];

        return $this->render('appointment/index.html.twig', [
            'form' => $form->createView(),
            'existingAppointments' => [], // À remplacer par la logique des rendez-vous existants si nécessaire
            'openDays' => $openDays,
            'availableSlots' => $availableSlots
        ]);
    }
    
    
    
    // Méthode pour générer un numéro unique de rendez-vous
    private function generateUniqueNumRdv(EntityManagerInterface $entityManager): int
    {
        do {
            $numRdv = random_int(100000, 999999);
            $existingRdv = $entityManager->getRepository(RendezVous::class)->findOneBy(['num_rdv' => $numRdv]);
        } while ($existingRdv !== null);
    
        return $numRdv;
    }
    
    #[Route('/rendezvous/success', name: 'rendezvous_success')]
    public function success(): Response
    {
        return $this->render('appointment/success.html.twig');
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

    
   
#[Route('/rendezvous/available-slots', name: 'app_appointment_available_slots', methods: ['GET'])]
public function getAvailableSlotsAjax(Request $request, RendezVousRepository $rendezVousRepository, HoraireRepository $horaireRepository): JsonResponse
{
    // Récupérer la date depuis la requête
    $dateString = $request->query->get('date');
    $date = \DateTime::createFromFormat('Y-m-d', $dateString);

    // Vérifier si la date est valide
    if (!$date || $date->format('Y-m-d') !== $dateString) {
        return new JsonResponse(['error' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
    }

    // Récupérer tous les créneaux horaires possibles depuis la table Horaire
    $allSlots = $horaireRepository->findAll();

    // Convertir les objets Horaire en un tableau de chaînes de créneaux horaires
    $allSlotTimes = array_map(function ($horaire) {
        return $horaire->getSlot()->format('H:i:s');
    }, $allSlots);

    // Utiliser la méthode du repository pour obtenir les créneaux disponibles
    $availableSlots = $rendezVousRepository->findAvailableSlots($date, $allSlotTimes);

    return new JsonResponse(array_values($availableSlots));
}


}