<?php

namespace App\Controller\profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TimeRegisterController extends AbstractController
{
    #[Route('/time/register', name: 'app_time_register')]
    public function index(): Response
    {
        return $this->render('time_register/index.html.twig', [
            'controller_name' => 'TimeRegisterController',
        ]);
    }
}
