<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use Psr\Log\LoggerInterface;
use App\Service\EmailService;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/client')]
class ClientController extends AbstractController
{
    
    #[Route('/', name: 'app_client_index', methods: ['GET', 'POST'])]
    public function index(Request $request, ClientRepository $clientRepository, EmailService $emailService): Response
    {
        $clients = $clientRepository->findAll();
    
        if ($request->isMethod('POST')) {
            $clientIds = $request->request->all('clients');
            $subject = $request->request->get('subject');
            $content = $request->request->get('content');
    
            if (!empty($clientIds)) {
                $selectedClients = $clientRepository->findBy(['id' => $clientIds]);
                foreach ($selectedClients as $client) {
                    try {
                        $emailService->sendEmail($client->getEmail(), $subject, $content);
                    } catch (\Exception $e) {
                        $this->addFlash('error', "Erreur lors de l'envoi de l'e-mail à {$client->getEmail()}: {$e->getMessage()}");
                    }
                }
                $this->addFlash('success', 'Les e-mails ont été envoyés avec succès.');
            } else {
                $this->addFlash('error', 'Veuillez sélectionner au moins un client.');
            }
    
            return $this->redirectToRoute('app_client_index');
        }
    
        return $this->render('client/index.html.twig', [
            'clients' => $clients,
        ]);
    }

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_client_show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        return $this->render('client/show.html.twig', [
            'client' => $client,
            'reservations' => $client->getReservation(), 
        ]);
    }

    #[Route('/{id}/edit', name: 'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_client_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, ClientRepository $clientRepository, EntityManagerInterface $entityManager): Response
    {
        $client = $clientRepository->find($id);
        
        if (!$client) {
            throw $this->createNotFoundException('Client non trouvé');
        }
    
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
    }
    
}