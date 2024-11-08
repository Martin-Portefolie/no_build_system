<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TodosController extends AbstractController
{
    #[Route('/todos', name: 'app_todos')]
    public function index(): Response
    {
        return $this->render('todos/index.html.twig', [
            'controller_name' => 'TodosController',
        ]);
    }
}
