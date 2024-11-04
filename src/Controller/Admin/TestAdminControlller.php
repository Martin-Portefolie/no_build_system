<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestAdminControlller extends AbstractController
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
            'controller_name' => 'TestAdminControlller',
            'clientDataArray' => $clientDataArray,
        ]);
    }

    #[Route('/test/admin/new', name: 'admin_client_new')]
    public function new(Request $request): Response
    {
        // Create a new Client instance
        $client = new Client();

        // Create a form for the Client entity
        $form = $this->createForm(ClientType::class, $client);

        // Handle the form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the new client to the database
            $this->entityManager->persist($client);
            $this->entityManager->flush();

            // Redirect to the same page or a success page
            return $this->redirectToRoute('admin_client');
        }

        // Render the form in the template
        return $this->render('test_admin/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
