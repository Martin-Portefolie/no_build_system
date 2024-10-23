<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\Clock\now;

class BraveElephantController extends AbstractController
{
    #[Route('/', name: 'app_brave_elephant')]
    public function index(): Response
    {
        $date = new \DateTime();
        $weeknumber = intval($date->format('W'));
        $year = $date->format('o');
        return $this->render('brave_elephant/index.html.twig', [
            'controller_name' => 'BraveElephantController',
            'date' => $date,
            'weeknumber' => $weeknumber,
            'year' => $year
        ]);
    }
}
