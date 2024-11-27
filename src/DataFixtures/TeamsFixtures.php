<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Team;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TeamsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Fetch all users and projects
        $users = $manager->getRepository(User::class)->findAll();
        $projects = $manager->getRepository(Project::class)->findAll();

        if (count($users) < 3) {
            echo "Not enough users found. Please load UserFixtures first.\n";
            return;
        }

        if (count($projects) < 1) {
            echo "No projects found. Please load ProjectFixtures first.\n";
            return;
        }

        // Team names
        $teamNames = [
            'Team Alpha',
            'Team Beta',
            'Team Gamma',
            'Team Delta',
            'Team Epsilon',
            'Team Zeta',
            'Team Theta',
            'Team Iota',
            'Team Kappa',
            'Team Lambda',
        ];

        foreach ($teamNames as $name) {
            $team = new Team();
            $team->setName($name);

            // Add random users to the team (3-5 members)
            $teamUsers = array_rand($users, mt_rand(3, 5));
            if (!is_array($teamUsers)) {
                $teamUsers = [$teamUsers];
            }

            foreach ($teamUsers as $key) {
                $team->addUser($users[$key]);
            }

            // Associate a random project with the team
            $randomProject = $projects[array_rand($projects)];
            $team->addProject($randomProject);

            // Persist the team
            $manager->persist($team);
        }

        // Flush all changes to the database
        $manager->flush();

        echo "10 teams have been created, each with random users and associated projects.\n";
    }

    public function getDependencies()
    {
       return [
         ProjectFixtures::class
       ];
    }
}
