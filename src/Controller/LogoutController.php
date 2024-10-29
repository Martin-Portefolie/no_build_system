<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LogoutController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/logout', name: 'app_logout')]
    public function index(): Response
    {
    }
}
