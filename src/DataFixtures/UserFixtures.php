<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create an admin user
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('a@a.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        // Create regular users with real names
        $users = [
            ['username' => 'Bernie Bing', 'email' => 'b@b.com', 'password' => 'user123', 'roles' => ['ROLE_USER']],
            ['username' => 'Charlie Choe', 'email' => 'c@c.com', 'password' => 'user123', 'roles' => ['ROLE_USER']],
            ['username' => 'Dummy Dot', 'email' => 'd@d.com', 'password' => 'user123', 'roles' => ['ROLE_USER']],
            ['username' => 'Jane Smith', 'email' => 'janesmith@example.com', 'password' => 'user123', 'roles' => ['ROLE_USER']],
            ['username' => 'Robert Brown', 'email' => 'robertbrown@example.com', 'password' => 'user123', 'roles' => ['ROLE_USER']],
            ['username' => 'Emily Davis', 'email' => 'emilydavis@example.com', 'password' => 'user123', 'roles' => ['ROLE_USER']],
            ['username' => 'Michael Johnson', 'email' => 'michaeljohnson@example.com', 'password' => 'user123', 'roles' => ['ROLE_USER']],
        ];

        foreach ($users as $data) {
            $user = new User();
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
            $user->setRoles($data['roles']);
            $manager->persist($user);
        }

        // Persist all users to the database
        $manager->flush();

        echo "Admin and 7 regular users with real names have been created.\n";
    }
}
