<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
public function login(AuthenticationUtils $authenticationUtils): Response
{
    // Rediriger l'admin déjà connecté vers le dashboard admin
    if ($this->isGranted('ROLE_ADMIN')) {
        return $this->redirectToRoute('app_admin');
    }

    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername, 
        'error' => $error,
        'is_admin' => false
    ]);
}

    #[Route(path: '/admin/logout', name: 'app_logout')]
    public function logout(): void
    {
        
    }
}