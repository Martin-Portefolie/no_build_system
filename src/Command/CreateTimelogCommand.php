<?php

namespace App\Command;

use App\Entity\Timelog;
use App\Repository\TodoRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'create-timelog',
    description: 'Add a short description for your command',
)]
class CreateTimelogCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private TodoRepository $todoRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, TodoRepository $todoRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->todoRepository = $todoRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Username of the user logging time')
            ->addArgument('todoId', InputArgument::REQUIRED, 'ID of the todo')
            ->addArgument('hours', InputArgument::REQUIRED, 'Hours to log (0-23)')
            ->addArgument('minutes', InputArgument::REQUIRED, 'Minutes to log (0-59)')
            ->addArgument('date', InputArgument::REQUIRED, 'Date of the time log (Y-m-d)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Retrieve input values
        $username = $input->getArgument('username');
        $todoId = (int) $input->getArgument('todoId');
        $hours = (int) $input->getArgument('hours');
        $minutes = (int) $input->getArgument('minutes');
        $date = new \DateTime($input->getArgument('date'));

        if ($hours < 0 || $hours > 23 || $minutes < 0 || $minutes > 59) {
            $io->error('Invalid time input. Hours should be between 0-23, and minutes between 0-59.');
            return Command::FAILURE;
        }

        // Fetch user and todo entities
        $user = $this->userRepository->findOneBy(['username' => $username]);
        $todo = $this->todoRepository->find($todoId);

        if (!$user) {
            $io->error("User '{$username}' not found.");
            return Command::FAILURE;
        }

        if (!$todo) {
            $io->error("Todo with ID '{$todoId}' not found.");
            return Command::FAILURE;
        }

        // Check user’s team access to the todo’s project
        $project = $todo->getProject();
        $hasAccess = false;
        foreach ($user->getTeams() as $team) {
            if ($project->getTeams()->contains($team)) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            $io->error("User '{$username}' does not have access to this todo.");
            return Command::FAILURE;
        }

        // Create and save the timelog entry
        $timelog = new Timelog();
        $timelog->setUser($user);
        $timelog->setTodo($todo);
        $timelog->setTotalMinutes($hours * 60 + $minutes);
        $timelog->setDate($date);

        $this->entityManager->persist($timelog);
        $this->entityManager->flush();

        $io->success("Time logged for user '{$username}' on todo '{$todo->getName()}'.");
        return Command::SUCCESS;
    }
}
