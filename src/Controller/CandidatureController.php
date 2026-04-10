<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Entity\Candidature;
use App\Entity\OffreEmploi;
use App\Form\CandidatureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CandidatureController extends AbstractController
{
#[Route('/candidature', name: 'app_candidature_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $q = trim((string) $request->query->get('q', ''));
        $statut = trim((string) $request->query->get('statut', 'all'));

        $qb = $entityManager->getRepository(Candidature::class)
            ->createQueryBuilder('c')
            ->leftJoin('c.candidat', 'cand')
            ->leftJoin('cand.user', 'u')
            ->leftJoin('c.offreEmploi', 'o')
            ->orderBy('c.dateCandidature', 'DESC');

        if ($q !== '') {
            $qb->andWhere('
                u.nom LIKE :q OR
                u.prenom LIKE :q OR
                u.email LIKE :q OR
                o.titre LIKE :q
            ')
            ->setParameter('q', '%' . $q . '%');
        }

        if ($statut !== '' && $statut !== 'all') {
            if ($statut === 'en_attente') {
                $qb->andWhere('c.statut IN (:statuts)')
                   ->setParameter('statuts', ['en_attente', 'En attente', 'pending']);
            } elseif ($statut === 'acceptee') {
                $qb->andWhere('c.statut IN (:statuts)')
                   ->setParameter('statuts', ['acceptee', 'Acceptée', 'accepted']);
            } elseif ($statut === 'entretien') {
                $qb->andWhere('c.statut IN (:statuts)')
                   ->setParameter('statuts', ['entretien', 'Entretien', 'interview']);
            } elseif ($statut === 'refusee') {
                $qb->andWhere('c.statut IN (:statuts)')
                   ->setParameter('statuts', ['refusee', 'Refusée', 'rejected']);
            }
        }

        $candidatures = $qb->getQuery()->getResult();

        return $this->render('candidature/index.html.twig', [
            'candidatures' => $candidatures,
            'search' => $q,
            'current_statut' => $statut,
        ]);
    }

#[Route('/candidature/new', name: 'app_candidature_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $candidature = new Candidature();

    $form = $this->createForm(CandidatureType::class, $candidature, [
        'is_admin' => true,
    ]);
    $form->handleRequest($request);

    $cvFile = $form->has('cvFile') ? $form->get('cvFile')->getData() : null;

    if ($form->isSubmitted() && $form->isValid()) {
        if ($cvFile) {
            $originalName = $cvFile->getClientOriginalName();
            $size = $cvFile->getSize();
            $extension = $cvFile->guessExtension() ?: 'pdf';
            $newFilename = uniqid('', true) . '.' . $extension;

            $cvFile->move($this->getParameter('cv_directory'), $newFilename);

            $candidature->setCvPath($newFilename);
            $candidature->setCvOriginalName($originalName);
            $candidature->setCvSize($size);
            $candidature->setCvUploadedAt(new \DateTime());
        }

        $entityManager->persist($candidature);
        $entityManager->flush();

        $this->addFlash('success', 'Candidature ajoutée avec succès.');

        return $this->redirectToRoute('app_candidature_index');
    }

    return $this->render('candidature/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

    #[Route('/candidature/{id}', name: 'app_candidature_show', methods: ['GET'])]
    public function show(Candidature $candidature): Response
    {
        return $this->render('candidature/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    #[Route('/candidature/{id}/edit', name: 'app_candidature_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CandidatureType::class, $candidature, [
            'is_admin' => true,
        ]);
        $form->handleRequest($request);

        $cvFile = $form->get('cvFile')->getData();

        if ($form->isSubmitted() && $form->isValid()) {

if ($cvFile) {
    $originalName = $cvFile->getClientOriginalName();
    $size = $cvFile->getSize();
    $extension = $cvFile->guessExtension() ?: 'pdf';
    $newFilename = uniqid('', true) . '.' . $extension;

    $cvFile->move(
        $this->getParameter('cv_directory'),
        $newFilename
    );

    $candidature->setCvPath($newFilename);
    $candidature->setCvOriginalName($originalName);
    $candidature->setCvSize($size);
    $candidature->setCvUploadedAt(new \DateTime());
}
            $entityManager->flush();

            return $this->redirectToRoute('app_candidature_index');
        }

        return $this->render('candidature/edit.html.twig', [
            'form' => $form->createView(),
            'candidature' => $candidature,
        ]);
    }

    #[Route('/candidature/{id}', name: 'app_candidature_delete', methods: ['POST'])]
    public function delete(Request $request, Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $candidature->getId(), $request->request->get('_token'))) {
            $entityManager->remove($candidature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_candidature_index');
    }

    #[Route('/candidature/{id}/statut', name: 'app_candidature_statut', methods: ['POST'])]
    public function changeStatut(
        Request $request,
        Candidature $candidature,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('statut' . $candidature->getId(), $request->request->get('_token'))) {
            $statut = $request->request->get('statut');

            if (in_array($statut, ['en_attente', 'entretien', 'acceptee', 'refusee'], true)) {
                $candidature->setStatut($statut);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('app_candidature_show', [
            'id' => $candidature->getId(),
        ]);
    }

    // ✅ VERSION DYNAMIQUE
    #[Route('/mes-candidatures', name: 'candidat_candidatures', methods: ['GET'])]
    public function candidatCandidatures(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        $candidat = $entityManager
            ->getRepository(Candidat::class)
            ->findOneBy(['user' => $user]);

        if (!$candidat) {
            throw $this->createNotFoundException('Candidat non trouvé.');
        }

        $candidatures = $entityManager->getRepository(Candidature::class)
            ->findBy(['candidat' => $candidat], ['dateCandidature' => 'DESC']);

        return $this->render('candidat/candidature/index.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }

#[Route('/mes-candidatures/new/{offreId}', name: 'candidat_postuler', methods: ['GET', 'POST'])]
public function candidatPostuler(
    int $offreId,
    Request $request,
    EntityManagerInterface $entityManager
): Response {
    $user = $this->getUser();

    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté.');
    }

    $candidat = $entityManager->getRepository(Candidat::class)->findOneBy([
        'user' => $user
    ]);

    if (!$candidat) {
        throw $this->createNotFoundException('Candidat non trouvé.');
    }

    $offre = $entityManager->getRepository(OffreEmploi::class)->find($offreId);

    if (!$offre) {
        throw $this->createNotFoundException('Offre introuvable.');
    }

    $existing = $entityManager->getRepository(Candidature::class)->findOneBy([
        'candidat' => $candidat,
        'offreEmploi' => $offre,
    ]);

    if ($existing) {
        $this->addFlash('error', 'Vous avez déjà postulé à cette offre.');
        return $this->redirectToRoute('candidat_candidatures');
    }

    $candidature = new Candidature();
    $candidature->setCandidat($candidat);
    $candidature->setOffreEmploi($offre);
    $candidature->setDateCandidature(new \DateTime());
    $candidature->setStatut('en_attente');

    $form = $this->createForm(CandidatureType::class, $candidature, [
        'is_admin' => false,
    ]);
    $form->handleRequest($request);

    $cvFile = $form->get('cvFile')->getData();

    if ($form->isSubmitted() && $form->isValid()) {
        if ($cvFile) {
            $originalName = $cvFile->getClientOriginalName();
            $size = $cvFile->getSize();
            $extension = $cvFile->guessExtension() ?: 'pdf';
            $newFilename = uniqid('', true) . '.' . $extension;

            $cvFile->move($this->getParameter('cv_directory'), $newFilename);

            $candidature->setCvPath($newFilename);
            $candidature->setCvOriginalName($originalName);
            $candidature->setCvSize($size);
            $candidature->setCvUploadedAt(new \DateTime());
        }

        $entityManager->persist($candidature);
        $entityManager->flush();

        $this->addFlash('success', 'Votre candidature a été envoyée avec succès.');

        return $this->redirectToRoute('candidat_candidatures');
    }

    return $this->render('candidat/candidature/new.html.twig', [
        'form' => $form->createView(),
        'offre' => $offre,
    ]);
}
}
