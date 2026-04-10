<?php

namespace App\Controller;

use App\Entity\Salaire;
use App\Entity\Employe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/employe/salaires')]
#[IsGranted('ROLE_EMPLOYE')]
class EmployeeSalaireController extends AbstractController
{
    #[Route('/', name: 'app_employee_salaire_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $employe = $em->getRepository(Employe::class)
            ->findOneBy(['user' => $user]);

        $salaires = $em->getRepository(Salaire::class)
            ->createQueryBuilder('s')
            ->join('s.contract', 'c')
            ->where('c.employe = :emp')
            ->setParameter('emp', $employe)
            ->getQuery()
            ->getResult();

        return $this->render('employee/salaires/index.html.twig', [
            'salaires' => $salaires,
        ]);
    }
}