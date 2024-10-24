<?php

namespace App\Controller;

use App\Entity\Event;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/test/weeks/{week?}-{year?}', name: 'test_weeks', methods: ['GET'])]
    public function index(int $week = null, int $year = null, Request $request): Response
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

        // Calculate the start of the week based on the passed week and year
        $startOfWeek = new DateTime();
        $startOfWeek->setISODate($year, $week); // Set the start date to the given week
        $startOfWeek->setTime(0, 0, 0, 0); // Ensure the time is set to the start of the day

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

        // Fetch all events (this should eventually be filtered by week)
        $eventData = $this->entityManager->getRepository(Event::class)->findAll();

        // Loop through events and match them to their days
        foreach ($eventData as $event) {
            $eventStart = $event->getDateStart();
            $eventEnd = $event->getDateEnd();

            // Skip invalid events
            if ($eventEnd < $eventStart || trim($event->getTitle()) === "") {
                continue;
            }

            // Check which day the event belongs to and add it to the corresponding day in $data
            foreach ($data as &$day) {
                $dayStart = new DateTime($day['date']->format('Y-m-d'));
                $dayStart->setTime(0, 0, 0, 0);
                $dayEnd = new DateTime($day['date']->format('Y-m-d'));
                $dayEnd->setTime(23, 59, 59, 999);

                if ($eventStart <= $dayEnd && $eventEnd >= $dayStart) {
                    $day['events'][] = [
                        'id' => $event->getId(),
                        'title' => $event->getTitle(),
                        'description' => $event->getDescription(),
                        'start' => $eventStart->format('Y-m-d H:i:s'),
                        'end' => $eventEnd->format('Y-m-d H:i:s'),
                    ];
                }
            }
        }

        // Return the view with the weekly data and events
        return $this->render('test/index.html.twig', [
            'week' => $week,
            'year' => $year,
            'weeklyData' => $data, // Pass the weekly data with events to the template
        ]);
    }
}
