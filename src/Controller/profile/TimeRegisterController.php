<?php

namespace App\Controller\profile;
use App\Entity\Todo;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TimeRegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @throws \Exception
     */
    #[Route('/profile/time-register/{week?}-{year?}', name: 'app_time_register', methods: ['GET'])]
    public function index(Request $request, int $week = null, int $year = null): Response
    {
        $isTurboFrameRequest = $request->headers->get('Turbo-Frame') === 'week-frame';

        // Setting timezone, should be changed from .env !
        // Highly recommended that this value is only set on setup.
        $timezoneString = $_ENV['APP_TIMEZONE'] ?? 'Europe/Copenhagen'; // Use a default if not set
        $timezone = new DateTimeZone($timezoneString);
        $date = new DateTime('now', $timezone);

        // If no week or year is passed, default to the current ISO week and year
        if (!$week || !$year) {
            $today = $date;
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

        // Calculate the start of the week based on the passed week and year
        $startOfWeek = $date;
        $startOfWeek->setISODate($year, $week); // Set the start date to the given week
        $startOfWeek->setTime(0, 0, 0, 0);

        // Create a data array for the week (7 days)
        $data = [];
        for ($i = 0; $i < 7; $i++) {
            $currentDate = clone $startOfWeek;
            $currentDate->modify("+$i day");
            $data[] = [
                'date' => $currentDate,
                'events' => []
            ];
        }

        // Fetch all todos (this should eventually be filtered by week)
        $todoData = $this->entityManager->getRepository(Todo::class)->findAll();

        // Loop through events and match them to their days
        foreach ($todoData as $todo) {
            $todoStart = $todo->getDateStart();
            $todoEnd = $todo->getDateEnd();

            // Skip invalid events
            if ($todoEnd < $todoStart || trim($todo->getName()) === "") {
                continue;
            }

            foreach ($data as &$day) {
                $dayStart = new DateTime($day['date']->format('Y-m-d'));
                $dayStart->setTime(0, 0, 0, 0);
                $dayEnd = new DateTime($day['date']->format('Y-m-d'));
                $dayEnd->setTime(23, 59, 59, 999);

                if ($todoStart <= $dayEnd && $todoEnd >= $dayStart) {
                    $day['events'][] = [
                        'id' => $todo->getId(),
                        'title' => $todo->getName(),
                        'start' => $todoStart->format('Y-m-d H:i:s'),
                        'end' => $todoEnd->format('Y-m-d H:i:s'),
                    ];
                }
            }
        }

        if ($isTurboFrameRequest) {
            return $this->render('profile/time_register/_week_content.html.twig', [
                'week' => $week,
                'year' => $year,
                'weeklyData' => $data,
            ]);
        }

        // For full-page requests, render the entire page
        return $this->render('profile/time_register/index.html.twig', [
            'week' => $week,
            'year' => $year,
            'weeklyData' => $data,
        ]);
    }
}
