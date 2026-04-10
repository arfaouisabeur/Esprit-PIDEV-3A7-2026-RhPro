<?php

namespace App\Controller;

use App\Entity\Salaire;
use App\Form\SalaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/rh/salaires')]
#[IsGranted('ROLE_RH')]
class RhSalaireController extends AbstractController
{
      #[Route('/', name: 'app_rh_salaire_index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $qb = $em->getRepository(Salaire::class)
            ->createQueryBuilder('s')
            ->join('s.contract', 'c')
            ->join('c.employe', 'e')
            ->join('e.user', 'u');

        $salaires = $qb->getQuery()->getResult();

        // 🔥 COUNTERS
        $total = count($salaires);
        $paye = 0;
        $attente = 0;
        $totalMontant = 0;

        foreach ($salaires as $s) {
            $totalMontant += (float)$s->getMontant();

            if ($s->getStatut() === 'PAYE') {
                $paye++;
            } else {
                $attente++;
            }
        }

        return $this->render('rh/salaires/index.html.twig', [
            'salaires' => $salaires,
            'total_count' => $total,
            'paye_count' => $paye,
            'attente_count' => $attente,
            'total_montant' => $totalMontant
        ]);
    }

   #[Route('/new', name: 'app_rh_salaire_new')]
public function new(Request $request, EntityManagerInterface $em): Response
{
    $salaire = new Salaire();
    $form = $this->createForm(SalaireType::class, $salaire);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {

        if (!$salaire->getContract()) {
    $form->addError(new \Symfony\Component\Form\FormError(
        '⚠ Choisissez un employé depuis la recherche'
    ));
}

        // 🔥 DUPLICATE CHECK
        $existing = null;
        if ($salaire->getContract()) {
            $existing = $em->getRepository(Salaire::class)->findOneBy([
                'contract' => $salaire->getContract(),
                'mois' => $salaire->getMois(),
                'annee' => $salaire->getAnnee(),
            ]);
        }

        if ($existing) {
            $form->addError(new \Symfony\Component\Form\FormError(
                '⚠ Salaire déjà existant pour cet employé ce mois !'
            ));
        }

        if ($form->isValid() && !$existing && $salaire->getContract()) {
            $em->persist($salaire);
            $em->flush();

            return $this->redirectToRoute('app_rh_salaire_index');
        }
    }

    return $this->render('rh/salaires/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

     #[Route('/{id}/edit', name: 'app_rh_salaire_edit')]
    public function edit(Request $request, Salaire $salaire, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SalaireType::class, $salaire, [
            'is_edit' => true
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_rh_salaire_index');
        }

        return $this->render('rh/salaires/edit.html.twig', [
            'form' => $form->createView(),
            'salaire' => $salaire
        ]);
    }
    #[Route('/{id}', name: 'app_rh_salaire_delete', methods: ['POST'])]
    public function delete(Request $request, Salaire $salaire, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$salaire->getId(), $request->request->get('_token'))) {
            $em->remove($salaire);
            $em->flush();
        }

        return $this->redirectToRoute('app_rh_salaire_index');
    }
}