<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ClientFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $clients = [
            ['name' => 'Tech Innovations Ltd.', 'phone' => '12345678', 'email' => 'contact@techinnovations.com'],
            ['name' => 'Green Solutions Co.', 'phone' => '87654321', 'email' => 'info@greensolutions.com'],
            ['name' => 'Blue Ocean Enterprises', 'phone' => '23456789', 'email' => 'hello@blueocean.com'],
            ['name' => 'Peak Performance Ltd.', 'phone' => '34567890', 'email' => 'support@peakperformance.com'],
            ['name' => 'Future Vision Inc.', 'phone' => '45678901', 'email' => 'sales@futurevision.com'],
            ['name' => 'Bright Horizons LLC', 'phone' => '56789012', 'email' => 'team@brighthorizons.com'],
            ['name' => 'Urban Dynamics Group', 'phone' => '67890123', 'email' => 'office@urbandynamics.com'],
            ['name' => 'Global Ventures Ltd.', 'phone' => '78901234', 'email' => 'partners@globalventures.com'],
            ['name' => 'NextGen Technologies', 'phone' => '89012345', 'email' => 'info@nextgentech.com'],
            ['name' => 'EcoSmart Solutions', 'phone' => '90123456', 'email' => 'contact@ecosmart.com'],
        ];

        foreach ($clients as $data) {
            $client = new \App\Entity\Client();
            $client->setName($data['name']);
            $client->setContactPhone($data['phone']);
            $client->setContactEmail($data['email']);

            $manager->persist($client);
        }

        $manager->flush();

        echo "10 Clients have been added to the database.\n";
    }

    public function getDependencies()
    {
        return[
            UserFixtures::class,
        ];

    }
}
