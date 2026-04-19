<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Entity\RH;
use App\Form\ContractType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/rh/contracts')]
#[IsGranted('ROLE_RH')]
final class ContractController extends AbstractController
{
    #[Route('/', name: 'app_rh_contract_index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $search = $request->query->get('search');
        $status = $request->query->get('status');

        $qb = $em->getRepository(Contract::class)
            ->createQueryBuilder('c')
            ->join('c.employe', 'e')
            ->join('e.user', 'u');

        if ($search) {
            $qb->andWhere('LOWER(e.matricule) LIKE :search OR LOWER(u.nom) LIKE :search OR LOWER(u.prenom) LIKE :search')
               ->setParameter('search', '%'.strtolower($search).'%');
        }

        if ($status === 'active') {
            $qb->andWhere('c.date_fin IS NULL OR c.date_fin > :today')
               ->setParameter('today', date('Y-m-d'));
        }

        if ($status === 'expired') {
            $qb->andWhere('c.date_fin IS NOT NULL AND c.date_fin < :today')
               ->setParameter('today', date('Y-m-d'));
        }

        $contracts = $qb->getQuery()->getResult();

        return $this->render('rh/contracts/index.html.twig', [
            'contracts' => $contracts,
            'search' => $search,
            'status' => $status
        ]);
    }

 #[Route('/new', name: 'app_rh_contract_new')]
public function new(Request $request, EntityManagerInterface $em): Response
{
    $contract = new Contract();

    $user = $this->getUser();
    $rh = $em->getRepository(RH::class)->findOneBy(['user' => $user]);

    if (!$rh) {
        throw $this->createNotFoundException('RH not found');
    }

    $contract->setRh($rh);

    $form = $this->createForm(ContractType::class, $contract);
    $form->handleRequest($request);

    // 🔥 ALWAYS CHECK SUBMITTED FIRST
    if ($form->isSubmitted()) {

        // 🔥 ONLY IF VALID
        if ($form->isValid()) {

            // CHECK ACTIVE CONTRACT
            $existing = $em->getRepository(Contract::class)
                ->createQueryBuilder('c')
                ->where('c.employe = :emp')
                ->andWhere('c.date_fin IS NULL OR c.date_fin > :today')
                ->setParameter('emp', $contract->getEmploye())
                ->setParameter('today', date('Y-m-d'))
                ->getQuery()
                ->getOneOrNullResult();

            if ($existing) {
                $form->addError(new FormError(
                    '⚠ Cet employé a déjà un contrat actif !'
                ));
            } else {
                $em->persist($contract);
                $em->flush();

                return $this->redirectToRoute('app_rh_contract_index');
            }
        }

        // 🔥 DEBUG (optional)
        // dump($form->getErrors(true));
    }

    return $this->render('rh/contracts/new.html.twig', [
        'form' => $form->createView(),
    ]);
}
   #[Route('/{id}/edit', name: 'app_rh_contract_edit')]
public function edit(Request $request, Contract $contract, EntityManagerInterface $em): Response
{
    // 🔥 KEEP ORIGINAL EMPLOYEE (VERY IMPORTANT)
    $originalEmploye = $contract->getEmploye();

    $form = $this->createForm(ContractType::class, $contract, [
        'edit_mode' => true
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        // 🔥 FORCE ORIGINAL EMPLOYEE (NO CHANGE)
        $contract->setEmploye($originalEmploye);

        $em->flush();

        return $this->redirectToRoute('app_rh_contract_index');
    }

    return $this->render('rh/contracts/edit.html.twig', [
        'form' => $form->createView(),
        'contract' => $contract,
    ]);
}


    #[Route('/{id}', name: 'app_rh_contract_delete', methods: ['POST'])]
    public function delete(Request $request, Contract $contract, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contract->getId(), $request->request->get('_token'))) {
            $em->remove($contract);
            $em->flush();
        }

        return $this->redirectToRoute('app_rh_contract_index');
    }
    #[Route('/check-active/{id}', name: 'app_rh_contract_check')]
public function checkActive(int $id, EntityManagerInterface $em): Response
{
    // 🔥 FIX HERE
    $employe = $em->getRepository(\App\Entity\Employe::class)
        ->findOneBy(['userId' => $id]);

    if (!$employe) {
        return $this->json(['hasActive' => false]);
    }

    $existing = $em->getRepository(Contract::class)
        ->createQueryBuilder('c')
        ->where('c.employe = :emp')
        ->andWhere('c.date_fin IS NULL OR c.date_fin > :today')
        ->setParameter('emp', $employe)
        ->setParameter('today', date('Y-m-d'))
        ->getQuery()
        ->getOneOrNullResult();

    return $this->json([
        'hasActive' => $existing ? true : false
    ]);
}
}
