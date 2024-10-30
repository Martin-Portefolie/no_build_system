<?php

namespace App\Command;

use App\Entity\Team;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'create-team',
    description: 'Add a short description for your command',
)]
class CreateTeamCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('teamName', InputArgument::REQUIRED, 'Team Pegasus')
            ->addArgument('userEmails', InputArgument::IS_ARRAY, 'a@a.com c@c.com');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $teamName = $input->getArgument('teamName');
        $userEmails = $input->getArgument('userEmails');

        // Create the team
        $team = new Team();
        $team->setName($teamName);

        foreach ($userEmails as $email) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user) {
                $team->addUser($user);
                $io->success("Added user {$user->getUsername()} ({$email}) to team '{$teamName}'.");
            } else {
                $io->warning("User with email '{$email}' not found.");
            }
        }

        $this->entityManager->persist($team);
        $this->entityManager->flush();

        $io->success("Team '{$teamName}' has been created and users have been assigned.");

        return Command::SUCCESS;
    }
}
