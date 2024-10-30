<?php

namespace App\Command;

use App\Entity\Client;
use App\Entity\Project;
use App\Entity\Todo;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function PHPUnit\Framework\throwException;

#[AsCommand(
    name: 'create-todo',
    description: 'Add a short description for your command',
)]
class CreateTodoCommand extends Command
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->addArgument('project_id', InputArgument::REQUIRED, 'ID of the project to assign the Todo')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the Todo task')
            ->addOption('description', null, InputOption::VALUE_OPTIONAL, 'Description of the Todo')
            ->addOption('start_date', null, InputOption::VALUE_OPTIONAL, 'Start date of the Todo (Y-m-d format)')
            ->addOption('end_date', null, InputOption::VALUE_OPTIONAL, 'End date of the Todo (Y-m-d format)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Retrieve input arguments and options
        $projectId = $input->getArgument('project_id');
        $name = $input->getArgument('name');
        $description = $input->getOption('description');
        $startDate = $input->getOption('start_date') ? new \DateTime($input->getOption('start_date')) : new \DateTime();
        $endDate = $input->getOption('end_date') ? new \DateTime($input->getOption('end_date')) : (clone $startDate)->modify('+1 day');

        // Find the Project by ID
        $project = $this->entityManager->getRepository(Project::class)->find($projectId);

        if (!$project) {
            $io->error("Project with ID $projectId not found.");
            return Command::FAILURE;
        }

        // Create and set up the new Todo
        $todo = new Todo();
        $todo->setName($name);
        $todo->setProject($project);
        $todo->setDateStart($startDate);
        $todo->setDateEnd($endDate);

        if ($description) {
            $todo->setData(['description' => $description]);
        }

        // Persist the Todo
        $this->entityManager->persist($todo);
        $this->entityManager->flush();

        $io->success("Todo '$name' successfully created for project '{$project->getName()}' with ID: {$todo->getId()}");

        return Command::SUCCESS;
    }
}
