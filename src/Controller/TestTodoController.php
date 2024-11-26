<?php

namespace App\Controller;

use App\Entity\Todo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestTodoController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/profile/{week?}-{year?}', name: 'app_todo')]
    public function index(Request $request, int $week = null, int $year = null): Response
    {
        $timezone = new \DateTimeZone($_ENV['APP_TIMEZONE'] ?? 'Europe/Copenhagen');
        $currentDate = new \DateTime('now', $timezone);

        if (!$week || !$year) {
            $week = (int)$currentDate->format('W');
            $year = (int)$currentDate->format('Y');
        }

        if ($week < 1) {
            $week = 53;
            $year--;
        } elseif ($week > 53) {
            $week = 1;
            $year++;
        }

        $startOfWeek = new \DateTime();
        $startOfWeek->setISODate($year, $week)->setTime(0, 0, 0);

        $data = [];
        for ($i = 0; $i < 7; $i++) {
            $day = clone $startOfWeek;
            $day->modify("+$i day");

            $data[] = [
                'date' => $day,
                'todo' => [],
                'timelog' => [],
            ];
        }

        // Fetch the current user
        $user = $this->getUser();

        // Get the user's teams
        $userTeams = $user->getTeams();

        // Fetch Todos associated with Projects linked to the user's teams
        $todos = $this->entityManager->getRepository(Todo::class)->createQueryBuilder('t')
            ->join('t.project', 'p')
            ->join('p.teams', 'team')
            ->where('team IN (:teams)')
            ->setParameter('teams', $userTeams)
            ->getQuery()
            ->getResult();

        foreach ($todos as $todo) {
            foreach ($data as &$day) {
                $dayStart = (clone $day['date'])->setTime(0, 0);
                $dayEnd = (clone $day['date'])->setTime(23, 59, 59);

                if ($todo->getDateStart() <= $dayEnd && $todo->getDateEnd() >= $dayStart) {
                    $day['todo'][] = $todo;
                }

                foreach ($todo->getTimelogs() as $timelog) {
                    if ($timelog->getDate() >= $dayStart && $timelog->getDate() <= $dayEnd) {
                        $day['timelog'][] = [
                            'id' => $timelog->getId(),
                            'todo_id' => $todo->getId(),
                            'description' => $timelog->getDescription(),
                            'hours' => $timelog->getHours(),
                            'minutes' => $timelog->getMinutes(),
                            'date' => $timelog->getDate()->format('Y-m-d H:i:s'),
                        ];
                    }
                }
            }
        }

        $weeklyTotal = 0;
        foreach ($data as &$day) {
            $dayTotal = 0;
            foreach ($day['timelog'] as $timelog) {
                $dayTotal += $timelog['hours'] * 60 + $timelog['minutes'];
            }
            $day['dayTotal'] = $dayTotal;
            $weeklyTotal += $dayTotal;
        }

        $weeklyDates = array_map(function ($day) {
            return $day['date']->format('Y-m-d');
        }, $data);

        return $this->render('/test_todo/index.html.twig', [
            'week' => $week,
            'year' => $year,
            'weeklyData' => $data,
            'weeklyTotal' => $weeklyTotal,
            'todos' => $todos,
            'weeklyDates' => $weeklyDates,
        ]);
    }
}
