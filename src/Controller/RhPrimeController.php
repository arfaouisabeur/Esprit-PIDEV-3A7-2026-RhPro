<?php

namespace App\Controller;

use App\Entity\Prime;
use App\Form\PrimeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/rh/primes')]
#[IsGranted('ROLE_RH')]
class RhPrimeController extends AbstractController
{
    #[Route('/', name: 'app_rh_prime_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $primes = $em->getRepository(Prime::class)->findAll();

        // 🔥 SAFE TOTAL CALCULATION
        $totalMontant = 0;
        foreach ($primes as $p) {
            $totalMontant += (float) $p->getMontant();
        }

        return $this->render('rh/primes/index.html.twig', [
            'primes' => $primes,
            'total_montant' => $totalMontant
        ]);
    }

    #[Route('/new', name: 'app_rh_prime_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $prime = new Prime();
        $form = $this->createForm(PrimeType::class, $prime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($prime);
            $em->flush();

            return $this->redirectToRoute('app_rh_prime_index');
        }

        return $this->render('rh/primes/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}/edit', name: 'app_rh_prime_edit')]
public function edit(Prime $prime, Request $request, EntityManagerInterface $em): Response
{
    $form = $this->createForm(PrimeType::class, $prime);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $em->flush(); // 🔥 no persist needed

        return $this->redirectToRoute('app_rh_prime_index');
    }

    return $this->render('rh/primes/edit.html.twig', [
        'form' => $form->createView(),
        'prime' => $prime
    ]);
}
#[Route('/{id}/delete', name: 'app_rh_prime_delete', methods: ['POST'])]
public function delete(Request $request, Prime $prime, EntityManagerInterface $em): Response
{
    if ($this->isCsrfTokenValid('delete'.$prime->getId(), $request->request->get('_token'))) {

        $em->remove($prime);
        $em->flush();
    }

    return $this->redirectToRoute('app_rh_prime_index');
}
}