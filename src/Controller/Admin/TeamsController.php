<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamsController extends AbstractController
{
    #[Route('/teams', name: 'app_teams')]
    public function index(): Response
    {
        return $this->render('teams/index.html.twig', [
            'controller_name' => 'TeamsController',
        ]);
    }
}
