<?php

namespace App\Controller\profile;

use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TimeRegisterController extends AbstractController
{



    #[Route('/profile/timeregister/{week?}-{year?}', name: 'app_time_register', methods: ['GET'])]
    public function index(int $week = null, int $year = null): Response
    {

        $timezoneString = $_ENV['APP_TIMEZONE'] ?? 'Europe/Copenhagen'; // Use a default if not set
        $timezone = new DateTimeZone($timezoneString);

        if (!$week || !$year) {
            $today = new DateTime($timezone);
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

        return $this->render('profile/time_register/index.html.twig', [
            'controller_name' => 'TimeRegisterController',
            'week' => $week,
            'year' => $year,
        ]);
    }
}
