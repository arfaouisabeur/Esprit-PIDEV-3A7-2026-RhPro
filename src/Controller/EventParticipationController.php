<?php

namespace App\Controller;

use App\Entity\EventParticipation;
use App\Form\EventParticipationType;
use App\Repository\EventParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/event/participation')]
final class EventParticipationController extends AbstractController
{
    #[Route(name: 'app_event_participation_index', methods: ['GET'])]
    public function index(EventParticipationRepository $eventParticipationRepository): Response
    {
        return $this->render('event_participation/index.html.twig', [
            'event_participations' => $eventParticipationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_participation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $eventParticipation = new EventParticipation();
        $form = $this->createForm(EventParticipationType::class, $eventParticipation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($eventParticipation);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event_participation/new.html.twig', [
            'event_participation' => $eventParticipation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_participation_show', methods: ['GET'])]
    public function show(EventParticipation $eventParticipation): Response
    {
        return $this->render('event_participation/show.html.twig', [
            'event_participation' => $eventParticipation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_participation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EventParticipation $eventParticipation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventParticipationType::class, $eventParticipation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_event_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event_participation/edit.html.twig', [
            'event_participation' => $eventParticipation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_participation_delete', methods: ['POST'])]
    public function delete(Request $request, EventParticipation $eventParticipation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventParticipation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($eventParticipation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_participation_index', [], Response::HTTP_SEE_OTHER);
    }
}
