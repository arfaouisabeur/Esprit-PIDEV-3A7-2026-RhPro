<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\Users;
use App\Form\CandidatType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/candidat')]
final class CandidatController extends AbstractController
{
    #[Route(name: 'app_candidat_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $candidats = $entityManager
            ->getRepository(Candidat::class)
            ->findAll();

        return $this->render('candidat/index.html.twig', [
            'candidats' => $candidats,
        ]);
    }

    #[Route('/new', name: 'app_candidat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidat = new Candidat();

        $form = $this->createForm(CandidatType::class, $candidat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($candidat);
            $entityManager->flush();

            return $this->redirectToRoute('app_candidat_index');
        }

        return $this->render('candidat/new.html.twig', [
            'candidat' => $candidat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_candidat_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(Users::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $candidat = $em->getRepository(Candidat::class)->find($user);

        if (!$candidat) {
            throw $this->createNotFoundException('Candidat not found');
        }

        return $this->render('candidat/show.html.twig', [
            'candidat' => $candidat,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_candidat_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(Users::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $candidat = $em->getRepository(Candidat::class)->find($user);

        if (!$candidat) {
            throw $this->createNotFoundException('Candidat not found');
        }

        $form = $this->createForm(CandidatType::class, $candidat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_candidat_index');
        }

        return $this->render('candidat/edit.html.twig', [
            'candidat' => $candidat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_candidat_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(Users::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $candidat = $em->getRepository(Candidat::class)->find($user);

        if (!$candidat) {
            throw $this->createNotFoundException('Candidat not found');
        }

        if ($this->isCsrfTokenValid('delete'.$id, $request->request->get('_token'))) {
            $em->remove($candidat);
            $em->flush();
        }

        return $this->redirectToRoute('app_candidat_index');
    }
}