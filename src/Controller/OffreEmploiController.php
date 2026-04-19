<?php

namespace App\Controller;

use App\Entity\OffreEmploi;
use App\Form\OffreEmploiType;
use App\Repository\CandidatRepository;
use App\Repository\OffreEmploiRepository;
use App\Repository\RHRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OffreEmploiController extends AbstractController
{
    public function __construct(
        private readonly CandidatRepository $candidatRepo
    ) {}

    // ── Admin routes ─────────────────────────────────────────────────────────

    #[Route('/admin/offres', name: 'app_offre_emploi_index', methods: ['GET'])]
    public function adminIndex(Request $request, OffreEmploiRepository $repo): Response
    {
        $q = trim((string) $request->query->get('q', ''));
        return $this->render('offre_emploi/index.html.twig', [
            'offre_emplois' => $this->buildSearchQuery($repo, $q),
            'search'        => $q,
        ]);
    }

    #[Route('/admin/offres/ajax-search', name: 'app_offre_emploi_ajax_search', methods: ['GET'])]
    public function ajaxSearch(Request $request, OffreEmploiRepository $repo): Response
    {
        $q = trim((string) $request->query->get('q', ''));
        return $this->render('offre_emploi/_table.html.twig', [
            'offre_emplois' => $this->buildSearchQuery($repo, $q),
        ]);
    }

    #[Route('/admin/offres/new', name: 'app_offre_emploi_new', methods: ['GET', 'POST'])]
    public function adminNew(Request $request, EntityManagerInterface $em, RHRepository $rhRepository): Response
    {
        $offre       = new OffreEmploi();
        $currentUser = $this->getUser();
        $currentRh   = $currentUser ? $rhRepository->findOneBy(['user' => $currentUser]) : null;

        if ($currentRh) {
            $offre->setRh($currentRh);
        }

        $form = $this->createForm(OffreEmploiType::class, $offre, [
            'show_rh_field' => ($currentRh === null),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($offre->getRh() === null && $currentRh !== null) {
                $offre->setRh($currentRh);
            }
            if ($offre->getRh() === null) {
                $this->addFlash('error', "Impossible de déterminer le RH responsable. Connectez-vous en tant que RH.");
                return $this->render('offre_emploi/new.html.twig', [
                    'form'         => $form,
                    'offre_emploi' => $offre,
                    'geoapify_key' => $_ENV['GEOAPIFY_API_KEY'] ?? $_SERVER['GEOAPIFY_API_KEY'] ?? \getenv('GEOAPIFY_API_KEY') ?? '',
                ]);
            }
            $em->persist($offre);
            $em->flush();
            $this->addFlash('success', 'Offre créée avec succès.');
            return $this->redirectToRoute('app_offre_emploi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre_emploi/new.html.twig', [
            'form'         => $form,
            'offre_emploi' => $offre,
            'geoapify_key' => $_ENV['GEOAPIFY_API_KEY'] ?? $_SERVER['GEOAPIFY_API_KEY'] ?? \getenv('GEOAPIFY_API_KEY') ?? '',
        ]);
    }

    #[Route('/admin/offres/{id}', name: 'app_offre_emploi_show', methods: ['GET'])]
    public function adminShow(OffreEmploi $offre): Response
    {
        return $this->render('offre_emploi/show.html.twig', ['offre_emploi' => $offre]);
    }

    #[Route('/admin/offres/{id}/edit', name: 'app_offre_emploi_edit', methods: ['GET', 'POST'])]
    public function adminEdit(OffreEmploi $offre, Request $request, EntityManagerInterface $em, RHRepository $rhRepository): Response
    {
        $currentUser = $this->getUser();
        $currentRh   = $currentUser ? $rhRepository->findOneBy(['user' => $currentUser]) : null;

        $form = $this->createForm(OffreEmploiType::class, $offre, [
            'show_rh_field' => ($currentRh === null),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($offre->getRh() === null && $currentRh !== null) {
                $offre->setRh($currentRh);
            }
            $em->flush();
            $this->addFlash('success', 'Offre modifiée avec succès.');
            return $this->redirectToRoute('app_offre_emploi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre_emploi/edit.html.twig', [
            'form'         => $form,
            'offre_emploi' => $offre,
            'geoapify_key' => $_ENV['GEOAPIFY_API_KEY'] ?? $_SERVER['GEOAPIFY_API_KEY'] ?? \getenv('GEOAPIFY_API_KEY') ?? '',
        ]);
    }

    #[Route('/admin/offres/{id}', name: 'app_offre_emploi_delete', methods: ['POST'])]
    public function adminDelete(Request $request, OffreEmploi $offreEmploi, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $offreEmploi->getId(), $request->request->get('_token'))) {
            $em->remove($offreEmploi);
            $em->flush();
            $this->addFlash('success', 'Offre supprimée.');
        }
        return $this->redirectToRoute('app_offre_emploi_index', [], Response::HTTP_SEE_OTHER);
    }

    // ── Candidat public routes ────────────────────────────────────────────────

    #[Route('/offres', name: 'candidat_offres', methods: ['GET'])]
    public function candidatIndex(Request $request, OffreEmploiRepository $repo): Response
    {
        $q  = trim((string) $request->query->get('q', ''));
        $qb = $repo->createQueryBuilder('o')->orderBy('o.datePublication', 'DESC');
        if ($q !== '') {
            $qb->andWhere('o.titre LIKE :q OR o.localisation LIKE :q OR o.typeContrat LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        return $this->render('candidat/offre/index.html.twig', [
            'offres'       => $qb->getQuery()->getResult(),
            'search'       => $q,
            'favoriteIds'  => $this->resolveFavoriteIds(),
            'geoapify_key' => $_ENV['GEOAPIFY_API_KEY'] ?? $_SERVER['GEOAPIFY_API_KEY'] ?? \getenv('GEOAPIFY_API_KEY') ?? '',
        ]);
    }

    #[Route('/offres/{id}', name: 'candidat_offre_show', methods: ['GET'])]
    public function candidatShow(OffreEmploi $offre): Response
    {
        $favoriteIds = $this->resolveFavoriteIds();

        return $this->render('candidat/offre/show.html.twig', [
            'offre'        => $offre,
            'isFavorite'   => in_array($offre->getId(), $favoriteIds, true),
            'geoapify_key' => $_ENV['GEOAPIFY_API_KEY'] ?? $_SERVER['GEOAPIFY_API_KEY'] ?? \getenv('GEOAPIFY_API_KEY') ?? '',
        ]);
    }

    // ── Favorites ─────────────────────────────────────────────────────────────

    #[Route('/offres/{id}/favori', name: 'candidat_toggle_favori', methods: ['POST'])]
    public function toggleFavori(OffreEmploi $offre, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Vous devez être connecté.'], 401);
        }

        $candidat = $this->candidatRepo->findOneBy(['user' => $user]);

        if (!$candidat) {
            return $this->json(['success' => false, 'message' => 'Profil candidat introuvable.'], 403);
        }

        if ($candidat->hasOffreFavorite($offre)) {
            $candidat->removeOffreFavorite($offre);
            $isFavorite = false;
            $message    = 'Offre retirée de vos favoris.';
        } else {
            $candidat->addOffreFavorite($offre);
            $isFavorite = true;
            $message    = 'Offre ajoutée à vos favoris !';
        }

        $em->flush();

        return $this->json([
            'success'    => true,
            'isFavorite' => $isFavorite,
            'message'    => $message,
        ]);
    }

    #[Route('/mes-favoris', name: 'candidat_mes_favoris', methods: ['GET'])]
    public function mesFavoris(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $candidat = $this->candidatRepo->findOneBy(['user' => $user]);
        $offres   = $candidat ? $candidat->getOffresFavorites()->toArray() : [];

        return $this->render('candidat/offre/favoris.html.twig', [
            'offres'       => $offres,
            'favoriteIds'  => array_map(fn($o) => $o->getId(), $offres),
            'geoapify_key' => $_ENV['GEOAPIFY_API_KEY'] ?? $_SERVER['GEOAPIFY_API_KEY'] ?? \getenv('GEOAPIFY_API_KEY') ?? '',
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function resolveFavoriteIds(): array
    {
        $user = $this->getUser();
        if (!$user) {
            return [];
        }

        $candidat = $this->candidatRepo->findOneBy(['user' => $user]);
        if (!$candidat) {
            return [];
        }

        return array_map(
            fn($o) => $o->getId(),
            $candidat->getOffresFavorites()->toArray()
        );
    }

    private function buildSearchQuery(OffreEmploiRepository $repo, string $q): array
    {
        $qb = $repo->createQueryBuilder('o')->orderBy('o.datePublication', 'DESC');
        if ($q !== '') {
            $qb->andWhere('o.titre LIKE :q OR o.localisation LIKE :q OR o.typeContrat LIKE :q OR o.statut LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }
        return $qb->getQuery()->getResult();
    }
}
