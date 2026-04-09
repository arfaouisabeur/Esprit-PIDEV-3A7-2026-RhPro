<?php

namespace App\Controller;

use App\Entity\DemandeService;
use App\Entity\Reponse;
use App\Form\DemandeServiceType;
use App\Repository\DemandeServiceRepository;
use App\Repository\EmployeRepository;
use App\Repository\RHRepository;
use App\Repository\TypeServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/demande/service')]
final class DemandeServiceController extends AbstractController
{
    #[Route(name: 'app_demande_service_index', methods: ['GET'])]
    public function index(Request $request, DemandeServiceRepository $demandeServiceRepository, EmployeRepository $employeRepository): Response
    {
        $search    = $request->query->get('search', '');
        $searchBy  = $request->query->get('searchBy', 'all');
        $sortBy    = $request->query->get('sortBy', 'dateDemande');
        $sortDir   = strtolower($request->query->get('sortDir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowed = ['dateDemande', 'statut', 'type'];
        if (!in_array($sortBy, $allowed)) $sortBy = 'dateDemande';

        $qb = $demandeServiceRepository->createQueryBuilder('d')
            ->leftJoin('d.type', 't')
            ->leftJoin('d.employe', 'e')
            ->leftJoin('e.user', 'u')
            ->addSelect('t', 'e', 'u');

        // Employé : ne voit que SES propres demandes
        if ($this->isGranted('ROLE_EMPLOYE') && !$this->isGranted('ROLE_RH')) {
            $user    = $this->getUser();
            $employe = $user ? $employeRepository->findOneBy(['user' => $user]) : null;
            if ($employe) {
                $qb->andWhere('d.employe = :employe')->setParameter('employe', $employe);
            }
        }

        // Recherche
        if ($search !== '') {
            $s = '%' . $search . '%';
            if ($searchBy === 'statut') {
                $qb->andWhere('d.statut LIKE :s')->setParameter('s', $s);
            } elseif ($searchBy === 'type') {
                $qb->andWhere('t.nom LIKE :s')->setParameter('s', $s);
            } elseif ($searchBy === 'employe') {
                $qb->andWhere('u.prenom LIKE :s OR u.nom LIKE :s')->setParameter('s', $s);
            } else { // all
                $qb->andWhere('d.statut LIKE :s OR t.nom LIKE :s OR u.prenom LIKE :s OR u.nom LIKE :s')
                   ->setParameter('s', $s);
            }
        }

        // Tri — mapping camelCase → nom réel de la propriété PHP de l'entité
        $columnMap = [
            'dateDemande' => 'date_demande',
            'statut'      => 'statut',
        ];

        if ($sortBy === 'type') {
            $qb->orderBy('t.nom', $sortDir);
        } else {
            $doctrineCol = $columnMap[$sortBy] ?? 'date_demande';
            $qb->orderBy('d.' . $doctrineCol, $sortDir);
        }

        return $this->render('demande_service/index.html.twig', [
            'demande_services' => $qb->getQuery()->getResult(),
            'search'           => $search,
            'searchBy'         => $searchBy,
            'sortBy'           => $sortBy,
            'sortDir'          => $sortDir,
        ]);
    }

    #[Route('/new', name: 'app_demande_service_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        EmployeRepository $employeRepository,
        TypeServiceRepository $typeServiceRepository
    ): Response
    {
        $demandeService = new DemandeService();

        // Auto-fill employé + statut/date
        $user = $this->getUser();
        $employe = $user ? $employeRepository->findOneBy(['user' => $user]) : null;
        if ($employe) {
            $demandeService->setEmploye($employe);
        }
        $demandeService->setStatut('En attente');
        $demandeService->setDateDemande((new \DateTimeImmutable())->format('Y-m-d'));
        // Éviter INSERT avec titre NULL (colonne NOT NULL) avant soumission du type
        $demandeService->setTitre('Demande de service');

        $form = $this->createForm(DemandeServiceType::class, $demandeService);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Titre = libellé du type choisi (obligatoire via le formulaire)
            $type = $demandeService->getType();
            $demandeService->setTitre($type ? (string) $type->getNom() : 'Demande de service');
            $entityManager->persist($demandeService);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande de service a été soumise avec succès !');
            return $this->redirectToRoute('app_demande_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demande_service/new.html.twig', [
            'demande_service' => $demandeService,
            'form' => $form,
            'type_services_payload' => array_map(
                static fn($t) => ['id' => $t->getId(), 'nom' => $t->getNom(), 'categorie' => $t->getCategorie()],
                $typeServiceRepository->findAll()
            ),
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
    public function edit(
        Request $request,
        DemandeService $demandeService,
        EntityManagerInterface $entityManager,
        TypeServiceRepository $typeServiceRepository
    ): Response
    {
        $form = $this->createForm(DemandeServiceType::class, $demandeService);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type = $demandeService->getType();
            if ($type) {
                $demandeService->setTitre((string) $type->getNom());
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demande_service/edit.html.twig', [
            'demande_service' => $demandeService,
            'form' => $form,
            'type_services_payload' => array_map(
                static fn($t) => ['id' => $t->getId(), 'nom' => $t->getNom(), 'categorie' => $t->getCategorie()],
                $typeServiceRepository->findAll()
            ),
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

    // ─── RH : Accepter ou Refuser une demande de service ─────────────────────
    #[Route('/{id}/repondre', name: 'app_demande_service_repondre', methods: ['POST'])]
    public function repondre(
        Request $request,
        DemandeService $demandeService,
        EntityManagerInterface $entityManager,
        RHRepository $rhRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_RH');

        $decision    = $request->request->get('decision');   // 'approuvé' ou 'refusé'
        $commentaire = $request->request->get('commentaire', '');

        if (!in_array($decision, ['approuvé', 'refusé'])) {
            $this->addFlash('error', 'Décision invalide.');
            return $this->redirectToRoute('app_demande_service_index');
        }

        // 1) Statut lisible côté employé
        $demandeService->setStatut($decision === 'approuvé' ? 'Accepté' : 'Refusé');

        // 2) Créer ou mettre à jour la Reponse associée
        $reponseRepo = $entityManager->getRepository(Reponse::class);
        $reponse     = $reponseRepo->findOneBy(['demande_service' => $demandeService]) ?? new Reponse();

        $user = $this->getUser();
        $rh   = $user ? $rhRepository->findOneBy(['user' => $user]) : null;

        $reponse->setDecision($decision);
        $reponse->setCommentaire($commentaire ?: null);
        $reponse->setRh($rh);
        $reponse->setEmploye($demandeService->getEmploye());
        $reponse->setDemandeService($demandeService);

        $entityManager->persist($reponse);
        $entityManager->flush();

        $label = $decision === 'approuvé' ? '✅ approuvée' : '❌ refusée';
        $this->addFlash('success', 'Demande de service ' . $label . ' avec succès.');

        return $this->redirectToRoute('app_demande_service_index');
    }
}
