<?php

namespace App\DataFixtures;

use App\Entity\Timelog;
use App\Entity\Todo;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TimelogFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Fetch all users and todos
        $users = $manager->getRepository(User::class)->findAll();
        $todos = $manager->getRepository(Todo::class)->findAll();

        if (count($users) < 1 || count($todos) < 1) {
            echo "Not enough users or todos found. Please load UserFixtures and TodoFixtures first.\n";
            return;
        }

        // Sample descriptions for timelogs
        $descriptions = [
            'Worked on initial implementation',
            'Fixed reported bugs',
            'Improved performance of the feature',
            'Conducted testing and debugging',
            'Wrote additional documentation',
            'Integrated with external API',
            'Added new features as requested',
            'Cleaned up and refactored code',
            'Updated UI for better usability',
            'Resolved compatibility issues',
        ];

        // Generate 20 timelogs
        for ($i = 1; $i <= 20; $i++) {
            $timelog = new Timelog();

            // Assign random user and todo
            $user = $users[array_rand($users)];
            $todo = $todos[array_rand($todos)];

            // Randomly set hours and minutes
            $hours = mt_rand(0, 8);
            $minutes = mt_rand(0, 59);

            // Randomly set the date (within 30 days before or after today)
            $dateOffset = mt_rand(-30, 30);
            $date = new \DateTimeImmutable("now +{$dateOffset} days");

            // Set properties
            $timelog->setUser($user);
            $timelog->setTodo($todo);
            $timelog->setTotalMinutes($hours * 60 + $minutes);
            $timelog->setDate($date);
            $timelog->setDescription($descriptions[array_rand($descriptions)]);

            // Persist timelog
            $manager->persist($timelog);
        }

        // Save all timelogs
        $manager->flush();

        echo "20 timelogs have been created and assigned to random users and todos.\n";
    }

    public function getDependencies()
    {
       return [TodoFixtures::class];
    }
}
