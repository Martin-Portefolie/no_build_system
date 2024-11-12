<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use App\Entity\User;
use App\Form\ClientType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UsersController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/users', name: 'admin_users')]
    public function index(): Response
    {
        $userdata = $this->entityManager->getRepository(User::class)->findAll();

        foreach ($userdata as $user){
            $userDataArray[] = [
                'id' => $user->getId(),
                'name' => $user->getUsername(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'team' => $user->getTeams(),
                'timelog' => $user->getTimelogs(),
                'role' => $user->getRoles(),

            ];
        }


        return $this->render('admin/users/index.html.twig', [
            'controller_name' => 'UsersController',
            'users' => $userDataArray
            ]);
    }
    #[Route('/user/new', name: 'admin_user_post', methods: ['POST'])]
    public function new(Request $request) : Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_user');
        }

        return $this->render('admin/client/new.html.twig', [
            'form' => $form->createView(),
        ]);

    }
}
