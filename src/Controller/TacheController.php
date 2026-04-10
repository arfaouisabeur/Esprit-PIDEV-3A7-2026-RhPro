<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Entity\Projet;
use App\Form\TacheType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tache')]
final class TacheController extends AbstractController
{
    /**
     * RH → voit toutes les tâches
     */
    #[Route(name: 'app_tache_index', methods: ['GET'])]
    #[IsGranted('ROLE_RH')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $taches = $entityManager
            ->getRepository(Tache::class)
            ->findAll();

        return $this->render('tache/index.html.twig', [
            'taches' => $taches,
        ]);
    }

    /**
     * Employé → liste les tâches d'un projet spécifique
     * Accessible via le bouton "Gérer les tâches" depuis employe_index.html.twig
     */
    #[Route('/projet/{projetId}', name: 'app_tache_par_projet', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function tachesParProjet(
        int $projetId,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $employe = $user->getEmploye();

        $projet = $entityManager->getRepository(Projet::class)->find($projetId);

        if (!$projet) {
            throw $this->createNotFoundException('Projet introuvable.');
        }

        // Sécurité : l'employé doit être le responsable du projet (sauf si ROLE_RH)
        if (!$this->isGranted('ROLE_RH')) {
            if ($projet->getResponsableEmploye() !== $employe) {
                $this->addFlash('error', 'Vous n\'êtes pas autorisé à accéder à ce projet.');
                return $this->redirectToRoute('app_projet_employe_index');
            }
        }

        $taches = $entityManager
            ->getRepository(Tache::class)
            ->findBy(['projet' => $projet]);

        return $this->render('tache/employe_index.html.twig', [
            'taches'  => $taches,
            'projet'  => $projet,
        ]);
    }

    /**
     * RH → créer une tâche (formulaire complet)
     */
    #[Route('/new', name: 'app_tache_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RH')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache, ['is_employe' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tache);
            $entityManager->flush();

            $this->addFlash('success', 'Tâche créée avec succès.');
            return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/new.html.twig', [
            'tache'  => $tache,
            'form'   => $form,
            'projet' => null,
        ]);
    }

    /**
     * Employé → créer une tâche dans un projet spécifique
     * Le projet et l'employé sont pré-remplis automatiquement
     */
    #[Route('/new/projet/{projetId}', name: 'app_tache_new_pour_projet', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_EMPLOYE')]
    public function newPourProjet(
        int $projetId,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $employe = $user->getEmploye();

        $projet = $entityManager->getRepository(Projet::class)->find($projetId);

        if (!$projet) {
            throw $this->createNotFoundException('Projet introuvable.');
        }

        // Sécurité : seul le responsable du projet peut créer des tâches
        if ($projet->getResponsableEmploye() !== $employe) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à créer des tâches pour ce projet.');
            return $this->redirectToRoute('app_projet_employe_index');
        }

        $tache = new Tache();
        $tache->setProjet($projet);
        $tache->setEmploye($employe);

        $form = $this->createForm(TacheType::class, $tache, ['is_employe' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tache);
            $entityManager->flush();

            $this->addFlash('success', 'Tâche créée avec succès.');
            return $this->redirectToRoute('app_tache_par_projet', [
                'projetId' => $projet->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/new.html.twig', [
            'tache'  => $tache,
            'form'   => $form,
            'projet' => $projet,
        ]);
    }

    /**
     * RH + Employé → voir le détail d'une tâche
     */
    #[Route('/{id}', name: 'app_tache_show', methods: ['GET'])]
    public function show(Tache $tache): Response
    {
        return $this->render('tache/show.html.twig', [
            'tache' => $tache,
        ]);
    }

    /**
     * RH + Employé → modifier une tâche
     * L'employé ne peut modifier que les tâches de ses propres projets
     */
    #[Route('/{id}/edit', name: 'app_tache_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($user->isEmploye()) {
            $employe = $user->getEmploye();
            if ($tache->getProjet()?->getResponsableEmploye() !== $employe) {
                $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier cette tâche.');
                return $this->redirectToRoute('app_projet_employe_index');
            }
        }

        $form = $this->createForm(TacheType::class, $tache, [
            'is_employe' => $user->isEmploye(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Tâche modifiée avec succès.');

            // Redirection selon le rôle
            if ($user->isEmploye() && $tache->getProjet()) {
                return $this->redirectToRoute('app_tache_par_projet', [
                    'projetId' => $tache->getProjet()->getId(),
                ], Response::HTTP_SEE_OTHER);
            }
            return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/edit.html.twig', [
            'tache' => $tache,
            'form'  => $form,
        ]);
    }

    /**
     * RH + Employé → supprimer une tâche
     * L'employé ne peut supprimer que les tâches de ses propres projets
     */
    #[Route('/{id}', name: 'app_tache_delete', methods: ['POST'])]
    public function delete(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $projetId = $tache->getProjet()?->getId();

        if ($user->isEmploye()) {
            $employe = $user->getEmploye();
            if ($tache->getProjet()?->getResponsableEmploye() !== $employe) {
                $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer cette tâche.');
                return $this->redirectToRoute('app_projet_employe_index');
            }
        }

        if ($this->isCsrfTokenValid('delete'.$tache->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tache);
            $entityManager->flush();

            $this->addFlash('success', 'Tâche supprimée avec succès.');
        }

        // Redirection selon le rôle
        if ($user->isEmploye() && $projetId) {
            return $this->redirectToRoute('app_tache_par_projet', [
                'projetId' => $projetId,
            ], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
    }
}