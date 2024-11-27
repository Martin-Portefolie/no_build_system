<?php

namespace App\DataFixtures;

use App\Entity\Todo;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TodoFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Fetch all existing projects
        $projects = $manager->getRepository(Project::class)->findAll();

        if (count($projects) < 1) {
            echo "No projects found. Please load ProjectFixtures first.\n";
            return;
        }

        // Creative todo names
        $todoNames = [
            'Design Kanban Board',
            'Implement User Authentication',
            'Create API Documentation',
            'Refactor Codebase',
            'Optimize Database Queries',
            'Set Up CI/CD Pipeline',
            'Develop Frontend Components',
            'Write Unit Tests',
            'Integrate Third-party APIs',
            'Conduct Security Audit',
            'Plan Product Launch',
            'Fix Reported Bugs',
            'Prepare Release Notes',
            'Organize Team Meeting',
            'Review Code Changes',
            'Monitor System Performance',
            'Improve UX Design',
            'Develop Mobile App',
            'Implement Payment Gateway',
            'Add Dark Mode Support',
            'Localize Application Content',
            'Perform Load Testing',
            'Research Market Trends',
            'Update Project Documentation',
            'Optimize Image Compression',
            'Enhance Dashboard Features',
            'Implement Push Notifications',
            'Redesign Landing Page',
            'Fix Cross-browser Issues',
            'Conduct User Feedback Survey',
            'Migrate Legacy Code',
            'Create Onboarding Tutorial',
            'Deploy New Version',
            'Improve Search Functionality',
            'Automate Deployment Process',
            'Develop Real-time Chat',
            'Audit Data Privacy Policies',
            'Add Multi-language Support',
            'Configure Backup System',
            'Enhance Security Protocols',
        ];

        // Generate Todos
        foreach ($todoNames as $name) {
            $todo = new Todo();

            // Set Todo name
            $todo->setName($name);

            // Generate random dateStart (30 days in past or future)
            $daysOffset = mt_rand(-30, 30);
            $dateStart = new \DateTimeImmutable("now +{$daysOffset} days");

            // Generate dateEnd (1 to 10 days after dateStart)
            $dateEnd = $dateStart->modify("+" . mt_rand(1, 10) . " days");

            $todo->setDateStart($dateStart);
            $todo->setDateEnd($dateEnd);

            // Assign a random project
            $randomProject = $projects[array_rand($projects)];
            $todo->setProject($randomProject);

            // Persist the Todo
            $manager->persist($todo);
        }

        // Save all Todos
        $manager->flush();

        echo "40 creative Todos with randomized dates have been created and assigned to random projects.\n";
    }

    public function getDependencies()
    {
     return [TeamsFixtures::class];
    }
}
