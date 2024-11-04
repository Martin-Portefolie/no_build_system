<?php

namespace App\Controller;

use App\Entity\TestAutosave;
use App\Repository\TestAutosaveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestAutosaveController extends AbstractController
{
    #[Route('/autosave-form/{id?}', name: 'autosave_form', methods: ['GET'])]
    public function index(?int $id, TestAutosaveRepository $repository): Response
    {
        $testAutosave = $id ? $repository->find($id) : null;
        $hours = $testAutosave ? $testAutosave->getHours() : 0;
        $minutes = $testAutosave ? $testAutosave->getMinutes() : 0;

        // Render the form with existing hours and minutes if the record exists
        return $this->render('test_autosave/index.html.twig', [
            'hours' => $hours,
            'minutes' => $minutes,
            'id' => $id,
        ]);
    }

    #[Route('/testautosave', name: 'test_autosave_post', methods: ['POST'])]
    public function save(Request $request, EntityManagerInterface $entityManager, TestAutosaveRepository $repository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $hours = $data['hours'] ?? 0;
        $minutes = $data['minutes'] ?? 0;
        $id = $data['id'] ?? null;

        if (!is_numeric($hours) || !is_numeric($minutes)) {
            return new JsonResponse(['error' => 'Hours and minutes must be numeric values.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Update existing record or create a new one
        $testAutosave = $id ? $repository->find($id) : new TestAutosave();
        $testAutosave->setHoursAndMinutes((int) $hours, (int) $minutes);

        $entityManager->persist($testAutosave);
        $entityManager->flush();

        return new JsonResponse([
            'status' => 'success',
            'id' => $testAutosave->getId(),
            'totalMinutes' => $testAutosave->getTotalMinutes(),
            'hours' => $testAutosave->getHours(),
            'minutes' => $testAutosave->getMinutes()
        ]);
    }
}
