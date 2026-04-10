<?php

namespace App\Controller;

use App\Entity\CongeTt;
use App\Entity\Reponse;
use App\Form\CongeTtType;
use App\Repository\CongeTtRepository;
use App\Repository\EmployeRepository;
use App\Repository\RHRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/conge/tt')]
final class CongeTtController extends AbstractController
{
    #[Route(name: 'app_conge_tt_index', methods: ['GET'])]
    public function index(
        Request $request,
        CongeTtRepository $congeTtRepository,
        EmployeRepository $employeRepository
    ): Response {
        $search   = $request->query->get('search', '');
        $searchBy = $request->query->get('searchBy', 'all');
        $sortBy   = $request->query->get('sortBy', 'id');
        $sortDir  = $request->query->get('sortDir', 'asc');

        $allowedSort = ['id', 'typeConge', 'dateDebut', 'dateFin', 'statut'];
        if (!in_array($sortBy, $allowedSort)) {
            $sortBy = 'id';
        }
        $sortDir = strtolower($sortDir) === 'desc' ? 'desc' : 'asc';

        $qb = $congeTtRepository->createQueryBuilder('c')
            ->leftJoin('c.employe', 'e')
            ->leftJoin('e.user', 'u')
            ->addSelect('e', 'u');

        // Employé : ne voit que SES propres demandes
        if ($this->isGranted('ROLE_EMPLOYE') && !$this->isGranted('ROLE_RH')) {
            $user    = $this->getUser();
            $employe = $employeRepository->findOneBy(['user' => $user]);
            if ($employe) {
                $qb->andWhere('c.employe = :employe')
                   ->setParameter('employe', $employe);
            }
        }

        // Recherche ciblée par critère
        if ($search !== '') {
            $s = '%' . $search . '%';
            if ($searchBy === 'type_conge') {
                $qb->andWhere('c.type_conge LIKE :s')->setParameter('s', $s);
            } elseif ($searchBy === 'statut') {
                $qb->andWhere('c.statut LIKE :s')->setParameter('s', $s);
            } elseif ($searchBy === 'employe') {
                $qb->andWhere('u.prenom LIKE :s OR u.nom LIKE :s')->setParameter('s', $s);
            } else { // all
                $qb->andWhere('c.type_conge LIKE :s OR c.statut LIKE :s OR c.description LIKE :s OR u.prenom LIKE :s OR u.nom LIKE :s')
                   ->setParameter('s', $s);
            }
        }

        $columnMap = [
            'typeConge' => 'type_conge',
            'dateDebut' => 'date_debut',
            'dateFin'   => 'date_fin',
            'statut'    => 'statut',
            'id'        => 'id',
        ];
        $doctrineCol = $columnMap[$sortBy] ?? 'id';
        $qb->orderBy('c.' . $doctrineCol, $sortDir);

        $conge_tts = $qb->getQuery()->getResult();

        return $this->render('conge_tt/index.html.twig', [
            'conge_tts' => $conge_tts,
            'search'    => $search,
            'searchBy'  => $searchBy,
            'sortBy'    => $sortBy,
            'sortDir'   => $sortDir,
        ]);
    }

    #[Route('/new', name: 'app_conge_tt_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EmployeRepository $employeRepository): Response
    {
        $congeTt = new CongeTt();

        $user = $this->getUser();
        if ($user) {
            $employe = $employeRepository->findOneBy(['user' => $user]);
            if ($employe) {
                $congeTt->setEmploye($employe);
            }
        }

        $congeTt->setStatut('En attente');

        $form = $this->createForm(CongeTtType::class, $congeTt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($congeTt);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande de congé a été soumise avec succès !');

            return $this->redirectToRoute('app_conge_tt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('conge_tt/new.html.twig', [
            'conge_tt' => $congeTt,
            'form'     => $form,
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
            'form'     => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_conge_tt_delete', methods: ['POST'])]
    public function delete(Request $request, CongeTt $congeTt, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $congeTt->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($congeTt);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_conge_tt_index', [], Response::HTTP_SEE_OTHER);
    }

    // ─── RH : Accepter ou Refuser une demande ────────────────────────────────
    #[Route('/{id}/repondre', name: 'app_conge_tt_repondre', methods: ['POST'])]
    public function repondre(
        Request $request,
        CongeTt $congeTt,
        EntityManagerInterface $entityManager,
        RHRepository $rhRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_RH');

        $decision    = $request->request->get('decision');
        $commentaire = $request->request->get('commentaire', '');

        if (!in_array($decision, ['approuvé', 'refusé'])) {
            $this->addFlash('error', 'Décision invalide.');
            return $this->redirectToRoute('app_conge_tt_index');
        }

        // ── Si REFUSÉ : supprimer automatiquement la demande ──────────────
        if ($decision === 'refusé') {
            $reponseRepo = $entityManager->getRepository(Reponse::class);
            $reponse     = $reponseRepo->findOneBy(['conge_tt' => $congeTt]);
            if ($reponse) {
                $entityManager->remove($reponse);
            }
            $entityManager->remove($congeTt);
            $entityManager->flush();

            $this->addFlash('success', '❌ Demande refusée et supprimée automatiquement.');
            return $this->redirectToRoute('app_conge_tt_index');
        }

        // ── Si ACCEPTÉ ────────────────────────────────────────────────────
        $congeTt->setStatut('Accepté');

        $reponseRepo = $entityManager->getRepository(Reponse::class);
        $reponse     = $reponseRepo->findOneBy(['conge_tt' => $congeTt]) ?? new Reponse();

        $user = $this->getUser();
        $rh   = $rhRepository->findOneBy(['user' => $user]);

        $reponse->setDecision('approuvé');
        $reponse->setCommentaire($commentaire ?: null);
        $reponse->setRh($rh);
        $reponse->setEmploye($congeTt->getEmploye());
        $reponse->setCongeTt($congeTt);

        $entityManager->persist($reponse);
        $entityManager->flush();

        $this->addFlash('success', '✅ Demande approuvée avec succès.');
        return $this->redirectToRoute('app_conge_tt_index');
    }

    // ─── RH : Supprimer manuellement une demande ─────────────────────────────
    #[Route('/{id}/rh-delete', name: 'app_conge_tt_rh_delete', methods: ['POST'])]
    public function rhDelete(
        Request $request,
        CongeTt $congeTt,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_RH');

        if ($this->isCsrfTokenValid('rh-delete' . $congeTt->getId(), $request->request->get('_token'))) {
            // Supprimer la réponse liée si elle existe
            $reponseRepo = $entityManager->getRepository(Reponse::class);
            $reponse     = $reponseRepo->findOneBy(['conge_tt' => $congeTt]);
            if ($reponse) {
                $entityManager->remove($reponse);
            }
            $entityManager->remove($congeTt);
            $entityManager->flush();
            $this->addFlash('success', '🗑️ Demande supprimée par le RH.');
        }

        return $this->redirectToRoute('app_conge_tt_index');
    }
}