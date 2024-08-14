<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
abstract class AdminBaseController extends AbstractController
{
    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        $parameters['is_admin'] = true;
        return parent::render($view, $parameters, $response);
    }
}