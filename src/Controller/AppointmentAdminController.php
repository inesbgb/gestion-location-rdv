<?php

namespace App\Controller;

use App\Entity\Jour;
use App\Entity\RendezVous;
use App\Form\RendezVous1Type;
use App\Service\EmailService;
use App\Controller\AdminBaseController;
use App\Repository\JourRepository;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/admin/appointment')]
class AppointmentAdminController extends AdminBaseController
{
    #[Route('/', name: 'app_appointment_admin_index', methods: ['GET'])]
    public function index(RendezVousRepository $rendezVousRepository): Response
    {
        $today = new \DateTime();
        $rendezVou = $rendezVousRepository->createQueryBuilder('r')
            ->where('r.date_rdv >= :today')
            ->setParameter('today', $today->format('Y-m-d H:i:s'))
            ->orderBy('r.date_rdv', 'ASC')
            ->addOrderBy('r.heure_rdv', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('appointment_admin/index.html.twig', [
            'rendez_vou' => $rendezVou,
        ]);
    }

    #[Route('/new', name: 'app_appointment_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,JourRepository $jourRepo): Response
    {
        $rendezVou = new RendezVous();
        $form = $this->createForm(RendezVous1Type::class, $rendezVou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rendezVou);
            $entityManager->flush();

            return $this->redirectToRoute('app_appointment_admin_index', [], Response::HTTP_SEE_OTHER);
        }
         // Récupérer les rendez-vous existants
         $existingAppointments = $entityManager->getRepository(RendezVous::class)->findAll();
         $openDaysEntities = $jourRepo->findOpenDays();
         $openDays = [];
         foreach ($openDaysEntities as $jour) {
             $openDays [] = $jour->getLibelle();
         }
        
        return $this->render('appointment_admin/new.html.twig', [
            'rendez_vou' => $rendezVou,
            'form' => $form,
            'existingAppointments' => $existingAppointments,
            "openDays"=>$openDays
        ]);
    }

   

    #[Route('/{id}', name: 'app_appointment_admin_show', methods: ['GET'])]
    public function show(RendezVous $rendezVou): Response
    {
        return $this->render('appointment_admin/show.html.twig', [
            'rendez_vou' => $rendezVou,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_appointment_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RendezVous $rendezVou, EntityManagerInterface $entityManager, EmailService $emailService, JourRepository $jourRepo): Response
    {
        $form = $this->createForm(RendezVous1Type::class, $rendezVou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
             // Envoyer l'email de confirmation de modification
        $emailService->sendConfirmationEmail(
            $rendezVou->getEmail(),
            'Confirmation de modification de rendez-vous',
            $this->renderView('emails/modify_confirmation.html.twig', ['rendezvous' => $rendezVou])
        );

        $this->addFlash('success', 'Le rendez-vous a été modifié et un email de confirmation a été envoyé au client.');


            return $this->redirectToRoute('app_appointment_admin_index', [], Response::HTTP_SEE_OTHER);
        }

         // Récupérer les rendez-vous existants
         $existingAppointments = $entityManager->getRepository(RendezVous::class)->findAll();
         $openDaysEntities = $jourRepo->findOpenDays();
         $openDays = [];
         foreach ($openDaysEntities as $jour) {
             $openDays [] = $jour->getLibelle();
         }

        return $this->render('appointment_admin/edit.html.twig', [
            'rendez_vou' => $rendezVou,
            'form' => $form,
            'existingAppointments' => $existingAppointments,
            "openDays"=>$openDays
        ]);
    }



    #[Route('/{id}', name: 'app_appointment_admin_delete', methods: ['POST'])]
    public function delete(Request $request, RendezVous $rendezVou, EntityManagerInterface $entityManager, EmailService $emailService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rendezVou->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rendezVou);
            $entityManager->flush();
              // Envoyer l'email de confirmation
        $emailService->sendConfirmationEmail(
            $rendezVou->getEmail(),
            'Confirmation d\'annulation de rendez-vous',
            $this->renderView('emails/cancel_confirmation.html.twig', ['rendezvous' => $rendezVou])
        );

        $this->addFlash('success', 'Le rendez-vous a été supprimé et un email de confirmation a été envoyé au client.');
    }

        return $this->redirectToRoute('app_appointment_admin_index', [], Response::HTTP_SEE_OTHER);
    }
 
   
}
