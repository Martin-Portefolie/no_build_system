<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Fetch all existing clients
        $clients = $manager->getRepository(Client::class)->findAll();

        if (empty($clients)) {
            echo "No clients found. Please load ClientFixtures first.\n";
            return;
        }

        // Project names and descriptions
        $projects = [
            ['name' => 'Apollo Development', 'description' => 'A cutting-edge technology initiative.'],
            ['name' => 'Greenfield Exploration', 'description' => 'Sustainable energy project.'],
            ['name' => 'Skyline Revamp', 'description' => 'Urban infrastructure modernization.'],
            ['name' => 'Quantum Leap', 'description' => 'Breakthroughs in quantum computing.'],
            ['name' => 'Blue Horizon', 'description' => 'Marine conservation project.'],
            ['name' => 'Solaris Grid', 'description' => 'Expanding renewable solar energy grids.'],
            ['name' => 'Echo AI', 'description' => 'AI-powered voice recognition systems.'],
            ['name' => 'Visionary Initiative', 'description' => 'Exploration of AR/VR technologies.'],
            ['name' => 'EcoShelter Program', 'description' => 'Affordable and sustainable housing.'],
            ['name' => 'Aurora Research', 'description' => 'Advancing renewable energy storage.'],
        ];

        foreach ($projects as $data) {
            // Assign a random client to each project
            $randomClient = $clients[array_rand($clients)];

            $project = new Project();
            $project->setName($data['name']);
            $project->setDescription($data['description']);
            $project->setClient($randomClient);
            $project->setActive(true);

            $manager->persist($project);
        }

        $manager->flush();

        echo "10 projects have been created and assigned to random clients.\n";
    }

    public function getDependencies()
    {
        return [
            ClientFixtures::class, // Load ClientFixtures first
        ];
    }
}
