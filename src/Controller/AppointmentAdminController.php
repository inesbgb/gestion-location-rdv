<?php

namespace App\Controller;

use App\Entity\Jour;
use App\Entity\RendezVous;
use App\Form\RendezVous1Type;
use App\Service\EmailService;
use App\Repository\JourRepository;
use App\Controller\AdminBaseController;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/admin/appointment')]
class AppointmentAdminController extends AdminBaseController
{
    #[Route('/', name: 'app_appointment_admin_index', methods: ['GET'])]
public function index(Request $request, RendezVousRepository $rendezVousRepository): Response
{
    $filterDate = $request->query->get('filter_date');
    $today = new \DateTime();

    $queryBuilder = $rendezVousRepository->createQueryBuilder('r')
        ->orderBy('r.date_rdv', 'ASC')
        ->addOrderBy('r.heure_rdv', 'ASC');

    if ($filterDate) {
        $filterDateTime = new \DateTime($filterDate);
        $queryBuilder
            ->andWhere('r.date_rdv >= :filterDateStart')
            ->andWhere('r.date_rdv < :filterDateEnd')
            ->setParameter('filterDateStart', $filterDateTime->format('Y-m-d 00:00:00'))
            ->setParameter('filterDateEnd', $filterDateTime->modify('+1 day')->format('Y-m-d 00:00:00'));
    } else {
        $queryBuilder
            ->andWhere('r.date_rdv >= :today')
            ->setParameter('today', $today->format('Y-m-d H:i:s'));
    }

    $rendezVou = $queryBuilder->getQuery()->getResult();

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
        // Définir le statut à "confirmé" (true)
        $rendezVou->setStatut(true);

        // Générer un numéro de rendez-vous unique
        $rendezVou->setNumRdv($this->generateUniqueNumRdv($entityManager));

        $entityManager->persist($rendezVou);
        $entityManager->flush();

        return $this->redirectToRoute('app_appointment_admin_index', [], Response::HTTP_SEE_OTHER);
    }

    // Récupérer les rendez-vous existants
    $existingAppointments = $entityManager->getRepository(RendezVous::class)->findAll();
    $openDaysEntities = $jourRepo->findOpenDays();
    $openDays = [];
    foreach ($openDaysEntities as $jour) {
        $openDays[] = $jour->getLibelle();
    }
    
    return $this->render('appointment_admin/new.html.twig', [
        'rendez_vou' => $rendezVou,
        'form' => $form,
        'existingAppointments' => $existingAppointments,
        "openDays" => $openDays
    ]);
}

private function generateUniqueNumRdv(EntityManagerInterface $entityManager): int
{
    do {
        $numRdv = random_int(100000, 999999);
        $existingRdv = $entityManager->getRepository(RendezVous::class)->findOneBy(['num_rdv' => $numRdv]);
    } while ($existingRdv !== null);

    return $numRdv;
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
               $rendezVou->setHeureRdv($form->get('heure_rdv')->getData());
        

        $rendezVou->setStatut($form->get('statut')->getData());
        
        
        $entityManager->persist($rendezVou);
        $entityManager->flush();

       
       

        $emailService->sendConfirmationEmail(
            $rendezVou->getEmail(),
            'Confirmation de modification de rendez-vous',
            $this->renderView('emails/modify_confirmation.html.twig', ['rendezvous' => $rendezVou])
        );

        $this->addFlash('success', 'Le rendez-vous a été modifié et un email de confirmation a été envoyé au client.');

        return $this->redirectToRoute('app_appointment_admin_index', [], Response::HTTP_SEE_OTHER);
    }

   
    $existingAppointments = $entityManager->getRepository(RendezVous::class)->findAll();
    $openDaysEntities = $jourRepo->findOpenDays();
    $openDays = [];
    foreach ($openDaysEntities as $jour) {
        $openDays[] = $jour->getLibelle();
    }

    return $this->render('appointment_admin/edit.html.twig', [
        'rendez_vou' => $rendezVou,
        'form' => $form,
        'existingAppointments' => $existingAppointments,
        'openDays' => $openDays
    ]);
}

    #[Route('/{id}', name: 'app_appointment_admin_delete', methods: ['POST'])]
    public function delete(Request $request, RendezVous $rendezVou, EntityManagerInterface $entityManager, EmailService $emailService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rendezVou->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rendezVou);
            $entityManager->flush();
            dd($rendezVou);
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
