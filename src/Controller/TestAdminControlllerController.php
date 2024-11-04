<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestAdminControlllerController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/test/admin/', name: 'admin_client')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $clientdata = $this->entityManager->getRepository(Client::class)->findAll();

        foreach ($clientdata as $clients){
            $clientDataArray[] = [
                'id' => $clients->getId(),
                'name' => $clients->getName(),
                'email' => $clients->getContactEmail(),
                'phone' => $clients->getContactPhone(),
                'projects' => $clients->getProjects(),
            ];
        }



        return $this->render('test_admin/index.html.twig', [
            'controller_name' => 'TestAdminControlllerController',
            'clientDataArray' => $clientDataArray,
        ]);
    }
}
