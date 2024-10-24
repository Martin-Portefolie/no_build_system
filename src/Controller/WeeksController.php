<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WeeksController extends AbstractController
{
    #[Route('/weeks/{week?}-{year?}', name: 'weeks', methods: ['GET'])]
    public function index(int $week = null, int $year = null): Response
    {
        // If no week or year is passed, default to the current ISO week and year
        if (!$week || !$year) {
            $today = new DateTime();
            $week = (int)$today->format('W');  // Get current ISO week number
            $year = (int)$today->format('Y');  // Get current year
        }

        // Handle week boundaries for both forward and backward transitions
        if ($week < 1) {
            $week = 53;  // Set to week 53 of the previous year
            $year--;
        } elseif ($week > 53) {
            $week = 1;   // Set to week 1 of the next year
            $year++;
        }





        // Just return the week and year to the template
        return $this->render('weeks/index.html.twig', [
            'week' => $week,
            'year' => $year,
        ]);
    }
}
