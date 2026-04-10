<?php
 
namespace App\Controller;
 
use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Dompdf\Dompdf;
use Dompdf\Options;
 
class ProjetController extends AbstractController
{
    /**
     * RH → voit tous les projets avec recherche (titre, responsable, statut)
     */
    #[Route('/rh/projet', name: 'app_projet_index', methods: ['GET'])]
    public function index(Request $request, ProjetRepository $projetRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
 
        if ($user && $user->isEmploye()) {
            return $this->redirectToRoute('app_projet_employe_index');
        }
 
        $q      = $request->query->get('q');
        $statut = $request->query->get('statut');
 
        $projets = $projetRepository->search($q, $statut ?: null);
        $stats = $projetRepository->getStatusStats();

        // Faciliter l'accès aux stats dans JS
        $statsData = [];
        foreach ($stats as $s) {
            $statsData[$s['statut'] ?: 'non_defini'] = (int)$s['count'];
        }
 
        if ($request->headers->get('X-Requested-With') === 'XMLHttpRequest') {
            return $this->render('projet/_projet_table.html.twig', [
                'projets' => $projets,
            ]);
        }
 
        return $this->render('projet/index.html.twig', [
            'projets'   => $projets,
            'q'         => $q,
            'statut'    => $statut,
            'statsData' => $statsData,
        ]);
    }

    /**
     * RH → Export des projets en PDF
     */
    #[Route('/rh/projet/export/pdf', name: 'app_projet_export_pdf', methods: ['GET'])]
    #[IsGranted('ROLE_RH')]
    public function exportPdf(Request $request, ProjetRepository $projetRepository): Response
    {
        $q = $request->query->get('q');
        $statut = $request->query->get('statut');
        $projets = $projetRepository->search($q, $statut ?: null);

        $html = $this->renderView('rh/projet_export_pdf.html.twig', [
            'projets' => $projets,
            'date' => new \DateTime(),
        ]);

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="export_projets_' . date('Y-m-d') . '.pdf"',
        ]);
    }

    /**
     * RH → Export des statistiques en PDF (Graphique SVG natif)
     */
    #[Route('/rh/projet/export/stats', name: 'app_projet_export_stats', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RH')]
    public function exportStatsPdf(ProjetRepository $projetRepository): Response
    {
        $stats = $projetRepository->getStatusStats();
        
        $total = 0;
        foreach ($stats as $s) { $total += $s['count']; }

        $colors = [
            'termine' => '#10b981',
            'en_cours' => '#f59e0b',
            'en_attente' => '#8b5cf6',
            'annule' => '#ef4444'
        ];

        $slices = [];
        $currentOffset = 0;
        $circumference = 753.9822; // Circonférence pour un rayon de 120
        
        foreach ($stats as $s) {
            $percentage = $total > 0 ? ($s['count'] / $total) * 100 : 0;
            
            // Dash = portion de la circonférence
            $dash = ($percentage / 100) * $circumference;
            
            // Offset = rotation pour commencer à la fin du segment précédent
            // On commence à -90 deg de base (SVG stroke-dashoffset logic)
            $offset = $circumference - (($currentOffset / 100) * $circumference);

            $slices[] = [
                'statut' => $s['statut'],
                'count' => $s['count'],
                'percentage' => round($percentage, 0),
                'offset' => $offset,
                'dash' => $dash,
                'circumference' => $circumference,
                'color' => $colors[$s['statut']] ?? '#cbd5e1'
            ];
            $currentOffset += $percentage;
        }

        $html = $this->renderView('rh/projet_stats_pdf.html.twig', [
            'stats'  => $stats,
            'slices' => $slices,
            'total'  => $total,
            'date'   => new \DateTime(),
        ]);

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="stats_projets_' . date('Y-m-d') . '.pdf"',
        ]);
    }
 
    /**
     * Employé → voit uniquement ses projets assignés avec statistiques de tâches
     */
    #[Route('/projet', name: 'app_projet_employe_index', methods: ['GET'])]
    #[IsGranted('ROLE_EMPLOYE')]
    public function mesProjects(EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $employe = $user->getEmploye();
 
        $projets = $entityManager
            ->getRepository(Projet::class)
            ->findBy(['responsable_employe' => $employe]);
 
        $projetsData = [];
        $totalProjectsCount = count($projets);
        $totalTasksCount = 0;
        $completedProjectsCount = 0;
 
        foreach ($projets as $projet) {
            $totalTasks = $entityManager->getRepository(\App\Entity\Tache::class)->count(['projet' => $projet]);
            $completedTasks = $entityManager->getRepository(\App\Entity\Tache::class)->count([
                'projet' => $projet,
                'statut' => 'terminee'
            ]);
            
            $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            if ($progress === 100 && $totalTasks > 0) {
                $completedProjectsCount++;
            }
            
            $totalTasksCount += $totalTasks;
            
            $projetsData[] = [
                'entity'         => $projet,
                'progress'       => $progress,
                'totalTasks'     => $totalTasks,
                'completedTasks' => $completedTasks
            ];
        }
 
        return $this->render('projet/employe_index.html.twig', [
            'projetsData'           => $projetsData,
            'totalProjects'         => $totalProjectsCount,
            'totalTasks'            => $totalTasksCount,
            'completedProjects'      => $completedProjectsCount,
        ]);
    }
 
    /**
     * RH uniquement → créer un nouveau projet
     */
    #[Route('/rh/projet/new', name: 'app_projet_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RH')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);
 
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($projet);
            $entityManager->flush();
 
            $this->addFlash('success', 'Projet créé avec succès.');
            return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
        }
 
        return $this->render('projet/new.html.twig', [
            'projet' => $projet,
            'form'   => $form,
        ]);
    }
 
    /**
     * RH uniquement → voir le détail d'un projet
     */
    #[Route('/rh/projet/{id}', name: 'app_projet_show', methods: ['GET'])]
    #[IsGranted('ROLE_RH')]
    public function show(Projet $projet): Response
    {
        return $this->render('projet/show.html.twig', [
            'projet' => $projet,
        ]);
    }
 
    /**
     * RH uniquement → modifier un projet
     */
    #[Route('/rh/projet/{id}/edit', name: 'app_projet_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RH')]
    public function edit(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);
 
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
 
            $this->addFlash('success', 'Projet modifié avec succès.');
            return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
        }
 
        return $this->render('projet/edit.html.twig', [
            'projet' => $projet,
            'form'   => $form,
        ]);
    }
 
    /**
     * RH uniquement → supprimer un projet
     */
    #[Route('/rh/projet/{id}', name: 'app_projet_delete', methods: ['POST'])]
    #[IsGranted('ROLE_RH')]
    public function delete(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $projet->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($projet);
            $entityManager->flush();
 
            $this->addFlash('success', 'Projet supprimé avec succès.');
        }
 
        return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
    }
 
    /**
     * RH → voir les tâches d'un projet spécifique dans une vue RH
     */
    #[Route('/rh/projet/{id}/taches', name: 'app_projet_taches_view', methods: ['GET'])]
    #[IsGranted('ROLE_RH')]
    public function projetTaches(Projet $projet, EntityManagerInterface $entityManager): Response
    {
        $taches = $entityManager->getRepository(\App\Entity\Tache::class)->findBy(['projet' => $projet]);
 
        return $this->render('rh/projet_taches.html.twig', [
            'projet' => $projet,
            'taches' => $taches,
        ]);
    }
}