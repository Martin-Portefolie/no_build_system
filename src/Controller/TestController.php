<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    #[Route('/test/weeks/new/{date}', name: 'test_week_new', methods: ['GET', 'POST'])]
    public function newEvent(Request $request, string $date): Response
    {
        try {
            $dateObject = new DateTime($date);
        } catch (\Exception $e) {
            return new Response('Invalid date format.', 400);
        }

        $event = new Event();
        $event->setDateStart($dateObject);

        // Set a default end time 1 hour after the start time
        $dateEnd = (clone $dateObject)->modify('+1 hour');
        $event->setDateEnd($dateEnd);

        // Use the EventType form class to create the form
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($event);
            $this->entityManager->flush();

            // Redirect after successful form submission
            return new RedirectResponse($this->generateUrl('test_weeks'), 303);
        }

        return $this->render('test/new.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'date' => $dateObject,
        ]);
    }
    #[Route('/test/weeks/edit/{id}', name: 'test_event_edit', methods: ['GET', 'POST'])]
    public function editEvent(Request $request, int $id): Response
    {
        $event = $this->entityManager->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('test_weeks');
        }

        return $this->render('test/edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }

    #[Route('/test/weeks/delete/{id}', name: 'test_event_delete', methods: ['POST'])]
    public function deleteEvent(Request $request, int $id): Response
    {
        $event = $this->entityManager->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }

        // Validate CSRF token
        if (!$this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('test_weeks');
        }

        // Delete the event
        $this->entityManager->remove($event);
        $this->entityManager->flush();

        // Redirect to the main view to reload the page
        return $this->redirectToRoute('test_weeks');
    }

}


