<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProjectController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/project', name: 'admin_project')]
    public function index(): Response
    {

        $projects = $this->entityManager->getRepository(Project::class)->findAll();

        foreach ($projects as $project) {
            $projectsDataArray[] = [
                'id' => $project->getId(),
                'name' => $project->getName(),
                'description' => $project->getDescription(),
                'client' => $project->getClient()->getName(),
                'teams' => $project->getTeams(),
                'todo' => $project->getTodos(),
                'active' => $project->isActive(),
            ];
        }

        return $this->render('admin/project/index.html.twig', [
            'projectDataArray' => $projectsDataArray,
        ]);
    }

    #[Route('/admin/project/update/{id}', name: 'admin_project_update', methods: ['POST'])]
    public function update(Request $request, int $id): Response
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        // Update project fields from the form
        $project->setName($request->request->get('name'));
        $project->setDescription($request->request->get('description'));
        $project->setActive($request->request->has('active'));

        $this->entityManager->flush();

        return $this->redirectToRoute('admin_project');
    }


    #[Route('/admin/project/new', name: 'admin_project_new')]
    public function new(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($project);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_project');
        }

        return $this->render('admin/project/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
