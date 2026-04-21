<?php

namespace App\Controller;

use App\Entity\CongeTt;
use App\Form\CongeTtType;
use App\Repository\CongeTtRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/conge/tt')]
final class CongeTtController extends AbstractController
{
    #[Route(name: 'app_conge_tt_index', methods: ['GET'])]
    public function index(CongeTtRepository $congeTtRepository): Response
    {
        return $this->render('conge_tt/index.html.twig', [
            'conge_tts' => $congeTtRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_conge_tt_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $congeTt = new CongeTt();
        $form = $this->createForm(CongeTtType::class, $congeTt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($congeTt);
            $entityManager->flush();

            return $this->redirectToRoute('app_conge_tt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('conge_tt/new.html.twig', [
            'conge_tt' => $congeTt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_conge_tt_show', methods: ['GET'])]
    public function show(CongeTt $congeTt): Response
    {
        return $this->render('conge_tt/show.html.twig', [
            'conge_tt' => $congeTt,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_conge_tt_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CongeTt $congeTt, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CongeTtType::class, $congeTt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_conge_tt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('conge_tt/edit.html.twig', [
            'conge_tt' => $congeTt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_conge_tt_delete', methods: ['POST'])]
    public function delete(Request $request, CongeTt $congeTt, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$congeTt->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($congeTt);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_conge_tt_index', [], Response::HTTP_SEE_OTHER);
    }
}
