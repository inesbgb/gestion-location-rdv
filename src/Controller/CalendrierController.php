<?php

namespace App\Controller;

use App\Repository\JourRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CalendrierController extends AbstractController
{
    #[Route('/calendrier', name: 'app_calendrier', methods: ['GET', 'POST'])]
    public function index(Request $request, JourRepository $jourRepository): Response
    {
        if ($request->isMethod('POST')) {
            $joursSemaine = $request->request->all('jours_semaine');

            if (is_array($joursSemaine)) {
                foreach ($joursSemaine as $libelle => $ouvert) {
                    $jour = $jourRepository->findOneBy(['libelle' => $libelle]);
                    if ($jour) {
                        $jour->setOuvert($ouvert === 'on');
                        $jourRepository->save($jour, true);
                    }
                }
            }

            $this->addFlash('success', 'Disponibilités mises à jour avec succès.');
            return $this->redirectToRoute('app_calendrier_index');
        }

        $jours = $jourRepository->findAll();

        return $this->render('calendrier/index.html.twig', [
            'joursSemaine' => $jours,
        ]);
    }
}
