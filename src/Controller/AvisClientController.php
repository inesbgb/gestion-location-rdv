<?php

namespace App\Controller;

use App\Entity\AvisClient;
use App\Form\AvisClientType;
use App\Repository\AvisClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/avis/client')]
class AvisClientController extends AbstractController
{
    #[Route('/', name: 'app_avis_client_index', methods: ['GET'])]
    public function index(AvisClientRepository $avisClientRepository): Response
    {
        return $this->render('avis_client/index.html.twig', [
            'avis_clients' => $avisClientRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_avis_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $avisClient = new AvisClient();
        $form = $this->createForm(AvisClientType::class, $avisClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($avisClient);
            $entityManager->flush();

            return $this->redirectToRoute('app_avis_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('avis_client/new.html.twig', [
            'avis_client' => $avisClient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_avis_client_show', methods: ['GET'])]
    public function show(AvisClient $avisClient): Response
    {
        return $this->render('avis_client/show.html.twig', [
            'avis_client' => $avisClient,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_avis_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AvisClient $avisClient, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvisClientType::class, $avisClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_avis_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('avis_client/edit.html.twig', [
            'avis_client' => $avisClient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_avis_client_delete', methods: ['POST'])]
    public function delete(Request $request, AvisClient $avisClient, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avisClient->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($avisClient);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_avis_client_index', [], Response::HTTP_SEE_OTHER);
    }
}
