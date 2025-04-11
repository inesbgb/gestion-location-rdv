<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
abstract class AdminBaseController extends AbstractController
{
    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        // Ajouter is_admin aux paramètres
        $parameters['is_admin'] = true;
        
        // Créer la réponse avec le rendu parent
        $response = parent::render($view, $parameters, $response);
        
        // Ajouter les en-têtes de sécurité
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        return $response;
    }
}