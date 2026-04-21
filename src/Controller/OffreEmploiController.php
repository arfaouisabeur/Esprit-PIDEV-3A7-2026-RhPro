<?php

namespace App\Controller;

use App\Entity\OffreEmploi;
use App\Form\OffreEmploiType;
use App\Repository\OffreEmploiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/offre/emploi')]
final class OffreEmploiController extends AbstractController
{
    #[Route(name: 'app_offre_emploi_index', methods: ['GET'])]
    public function index(OffreEmploiRepository $offreEmploiRepository): Response
    {
        return $this->render('offre_emploi/index.html.twig', [
            'offre_emplois' => $offreEmploiRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_offre_emploi_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offreEmploi = new OffreEmploi();
        $form = $this->createForm(OffreEmploiType::class, $offreEmploi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offreEmploi);
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_emploi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre_emploi/new.html.twig', [
            'offre_emploi' => $offreEmploi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_emploi_show', methods: ['GET'])]
    public function show(OffreEmploi $offreEmploi): Response
    {
        return $this->render('offre_emploi/show.html.twig', [
            'offre_emploi' => $offreEmploi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offre_emploi_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OffreEmploi $offreEmploi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreEmploiType::class, $offreEmploi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_emploi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre_emploi/edit.html.twig', [
            'offre_emploi' => $offreEmploi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_emploi_delete', methods: ['POST'])]
    public function delete(Request $request, OffreEmploi $offreEmploi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offreEmploi->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offreEmploi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_offre_emploi_index', [], Response::HTTP_SEE_OTHER);
    }
}
