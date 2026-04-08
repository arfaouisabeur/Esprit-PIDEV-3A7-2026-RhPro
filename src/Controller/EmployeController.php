<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Entity\Users;
use App\Form\EmployeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/employe')]
final class EmployeController extends AbstractController
{
    #[Route(name: 'app_employe_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $employes = $entityManager
            ->getRepository(Employe::class)
            ->findAll();

        return $this->render('employe/index.html.twig', [
            'employes' => $employes,
        ]);
    }

    #[Route('/new', name: 'app_employe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $employe = new Employe();
        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($employe);
            $entityManager->flush();

            return $this->redirectToRoute('app_employe_index');
        }

        return $this->render('employe/new.html.twig', [
            'employe' => $employe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_employe_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(Users::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $employe = $em->getRepository(Employe::class)->find($user);

        if (!$employe) {
            throw $this->createNotFoundException('Employe not found');
        }

        return $this->render('employe/show.html.twig', [
            'employe' => $employe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_employe_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(Users::class)->find($id);
        $employe = $em->getRepository(Employe::class)->find($user);

        if (!$employe) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_employe_index');
        }

        return $this->render('employe/edit.html.twig', [
            'employe' => $employe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_employe_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(Users::class)->find($id);
        $employe = $em->getRepository(Employe::class)->find($user);

        if (!$employe) {
            throw $this->createNotFoundException();
        }

        if ($this->isCsrfTokenValid('delete'.$id, $request->request->get('_token'))) {
            $em->remove($employe);
            $em->flush();
        }

        return $this->redirectToRoute('app_employe_index');
    }
}