<?php

namespace App\Controller;

use App\Entity\DemandeService;
use App\Form\DemandeServiceType;
use App\Repository\DemandeServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/demande/service')]
final class DemandeServiceController extends AbstractController
{
    #[Route(name: 'app_demande_service_index', methods: ['GET'])]
    public function index(DemandeServiceRepository $demandeServiceRepository): Response
    {
        return $this->render('demande_service/index.html.twig', [
            'demande_services' => $demandeServiceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_demande_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $demandeService = new DemandeService();
        $form = $this->createForm(DemandeServiceType::class, $demandeService);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($demandeService);
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demande_service/new.html.twig', [
            'demande_service' => $demandeService,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demande_service_show', methods: ['GET'])]
    public function show(DemandeService $demandeService): Response
    {
        return $this->render('demande_service/show.html.twig', [
            'demande_service' => $demandeService,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_demande_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DemandeService $demandeService, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DemandeServiceType::class, $demandeService);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demande_service/edit.html.twig', [
            'demande_service' => $demandeService,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demande_service_delete', methods: ['POST'])]
    public function delete(Request $request, DemandeService $demandeService, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demandeService->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($demandeService);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_demande_service_index', [], Response::HTTP_SEE_OTHER);
    }
}
