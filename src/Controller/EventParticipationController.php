<?php

namespace App\Controller;

use App\Entity\EventParticipation;
use App\Entity\Evenement;
use App\Repository\EventParticipationRepository;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/')]
final class EventParticipationController extends AbstractController
{
    // =========================
    // 🔵 CÔTÉ RH
    // =========================

    #[Route('/rh/participations', name: 'app_event_participation_index', methods: ['GET'])]
    #[IsGranted('ROLE_RH')]
    public function index(EventParticipationRepository $repo): Response
    {
        return $this->render('event_participation/index.html.twig', [
            'event_participations' => $repo->findAll(),
        ]);
    }

    #[Route('/rh/participations/evenement/{id}', name: 'app_event_participation_by_event', methods: ['GET'])]
    #[IsGranted('ROLE_RH')]
    public function byEvent(Evenement $evenement, EventParticipationRepository $repo): Response
    {
        return $this->render('event_participation/index.html.twig', [
            'event_participations' => $repo->findBy(['evenement' => $evenement]),
            'evenement' => $evenement,
        ]);
    }

    #[Route('/rh/participations/{id}/accept', name: 'app_event_participation_accept', methods: ['POST'])]
    #[IsGranted('ROLE_RH')]
    public function accept(
        Request $request,
        EventParticipation $participation,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('accept' . $participation->getId(), $request->getPayload()->getString('_token'))) {
            $participation->setStatut('accepte');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_participation_index');
    }

    #[Route('/rh/participations/{id}/refuse', name: 'app_event_participation_refuse', methods: ['POST'])]
    #[IsGranted('ROLE_RH')]
    public function refuse(
        Request $request,
        EventParticipation $participation,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('refuse' . $participation->getId(), $request->getPayload()->getString('_token'))) {
            $participation->setStatut('refuse');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_participation_index');
    }

    #[Route('/rh/participations/{id}/delete', name: 'app_event_participation_delete', methods: ['POST'])]
    #[IsGranted('ROLE_RH')]
    public function delete(
        Request $request,
        EventParticipation $participation,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $participation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_participation_index');
    }

    // =========================
    // 🟢 CÔTÉ EMPLOYÉ
    // =========================

    #[Route('/employe/evenements', name: 'app_employe_evenements', methods: ['GET'])]
    #[IsGranted('ROLE_EMPLOYE')]
    public function employeIndex(
        EvenementRepository $evenementRepo,
        EventParticipationRepository $participationRepo
    ): Response {
        $employe = $this->getUser()?->getEmploye();

        $evenements = $evenementRepo->findAll();

        $mesParticipations = [];

        if ($employe) {
            $participations = $participationRepo->findBy(['employe' => $employe]);

            foreach ($participations as $p) {
                $mesParticipations[$p->getEvenement()->getId()] = $p;
            }
        }

        return $this->render('evenement/employe/evenements.html.twig', [
            'evenements'        => $evenements,
            'mesParticipations' => $mesParticipations,
        ]);
    }

    /**
     * Recherche AJAX des événements pour l'espace employé.
     * GET /employe/evenements/search?q=terme&statut=a_venir
     */
    #[Route('/employe/evenements/search', name: 'app_employe_evenements_search', methods: ['GET'])]
    #[IsGranted('ROLE_EMPLOYE')]
    public function searchAjax(
        Request $request,
        EvenementRepository $evenementRepo,
        EventParticipationRepository $participationRepo
    ): JsonResponse {
        $q      = trim($request->query->get('q', ''));
        $filtre = $request->query->get('statut', 'tous');
        $today  = (new \DateTime())->format('Y-m-d');

        $employe = $this->getUser()?->getEmploye();

        // Récupérer les participations de l'employé
        $mesParticipationsRaw = [];
        if ($employe) {
            $parts = $participationRepo->findBy(['employe' => $employe]);
            foreach ($parts as $p) {
                $mesParticipationsRaw[$p->getEvenement()->getId()] = [
                    'statut' => $p->getStatut(),
                    'id'     => $p->getId(),
                ];
            }
        }

        // Récupérer tous les événements et filtrer en PHP
        $all = $evenementRepo->findAll();

        $results = [];
        foreach ($all as $ev) {
            // Filtrage par mot-clé (titre, lieu, description)
            if ($q !== '') {
                $haystack = mb_strtolower($ev->getTitre() . ' ' . $ev->getLieu() . ' ' . $ev->getDescription());
                if (mb_strpos($haystack, mb_strtolower($q)) === false) {
                    continue;
                }
            }

            // Filtrage par statut temporel
            $debut = $ev->getDateDebut();
            $fin   = $ev->getDateFin();

            if ($filtre === 'a_venir'  && !($debut > $today)) continue;
            if ($filtre === 'en_cours' && !($debut <= $today && $fin >= $today)) continue;
            if ($filtre === 'termine'  && !($fin < $today)) continue;

            // Badge
            if ($debut <= $today && $fin >= $today) {
                $badge = 'en_cours';
            } elseif ($debut > $today) {
                $badge = 'a_venir';
            } else {
                $badge = 'termine';
            }

            $participation = $mesParticipationsRaw[$ev->getId()] ?? null;

            $results[] = [
                'id'             => $ev->getId(),
                'titre'          => $ev->getTitre(),
                'lieu'           => $ev->getLieu(),
                'date_debut'     => $ev->getDateDebut(),
                'date_fin'       => $ev->getDateFin(),
                'description'    => $ev->getDescription(),
                'image_url'      => $ev->getImageUrl(),
                'activites'      => count($ev->getActivites()),
                'badge'          => $badge,
                'participation'  => $participation,
                'url_detail'     => $this->generateUrl('app_employe_evenement_show', ['id' => $ev->getId()]),
                'url_participer' => $this->generateUrl('app_employe_participer', ['id' => $ev->getId()]),
            ];
        }

        return new JsonResponse(['evenements' => $results, 'total' => count($results)]);
    }

    #[Route('/employe/evenement/{id}', name: 'app_employe_evenement_show', methods: ['GET'])]
    #[IsGranted('ROLE_EMPLOYE')]
    public function employeShow(
        Evenement $evenement,
        EventParticipationRepository $participationRepo
    ): Response {
        $employe = $this->getUser()?->getEmploye();

        $maParticipation = null;

        if ($employe) {
            $maParticipation = $participationRepo->findOneBy([
                'evenement' => $evenement,
                'employe'   => $employe,
            ]);
        }

        return $this->render('evenement/employe/evenement_show.html.twig', [
            'evenement'       => $evenement,
            'maParticipation' => $maParticipation,
        ]);
    }

    #[Route('/employe/evenement/{id}/participer', name: 'app_employe_participer', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYE')]
    public function participer(
        Request $request,
        Evenement $evenement,
        EventParticipationRepository $participationRepo,
        EntityManagerInterface $entityManager
    ): Response {
        $employe = $this->getUser()?->getEmploye();

        if (!$employe) {
            return $this->redirectToRoute('app_employe_evenements');
        }

        $existing = $participationRepo->findOneBy([
            'evenement' => $evenement,
            'employe'   => $employe,
        ]);

        if ($existing) {
            return $this->redirectToRoute('app_employe_evenements');
        }

        $participation = new EventParticipation();
        $participation->setEvenement($evenement);
        $participation->setEmploye($employe);
        $participation->setStatut('en_attente');
        $participation->setDateInscription((new \DateTime())->format('Y-m-d'));

        $entityManager->persist($participation);
        $entityManager->flush();

        return $this->redirectToRoute('app_employe_evenements');
    }

    #[Route('/employe/evenement/{id}/annuler', name: 'app_employe_annuler_participation', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYE')]
    public function annuler(
        Request $request,
        EventParticipation $participation,
        EntityManagerInterface $entityManager
    ): Response {
        $employe = $this->getUser()?->getEmploye();

        if ($participation->getEmploye() !== $employe) {
            return $this->redirectToRoute('app_employe_evenements');
        }

        if ($this->isCsrfTokenValid('annuler' . $participation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_employe_evenements');
    }

    // =========================
    // ⭐ MES PARTICIPATIONS
    // =========================

    #[Route('/employe/mes-participations', name: 'app_employe_mes_participations', methods: ['GET'])]
    #[IsGranted('ROLE_EMPLOYE')]
    public function mesParticipations(EventParticipationRepository $repo): Response
    {
        $employe = $this->getUser()?->getEmploye();

        $participations = [];

        if ($employe) {
            $participations = $repo->findBy(
                ['employe' => $employe],
                ['id' => 'DESC']
            );
        }

        return $this->render('evenement/employe/mes_participations.html.twig', [
            'participations' => $participations,
        ]);
    }
}