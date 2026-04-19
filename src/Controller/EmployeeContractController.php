<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Entity\Employe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// 🔥 PDF
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/employe/contracts')]
#[IsGranted('ROLE_EMPLOYE')]
class EmployeeContractController extends AbstractController
{
    #[Route('/', name: 'app_employee_contract_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // 🔥 SAFE METHOD (KEEP THIS)
        $employe = $em->getRepository(Employe::class)
            ->findOneBy(['user' => $user]);

        if (!$employe) {
            throw $this->createNotFoundException('Employe not found');
        }

        $contracts = $em->getRepository(Contract::class)
            ->findBy(['employe' => $employe]);

        return $this->render('employee/contracts/index.html.twig', [
            'contracts' => $contracts,
        ]);
    }

   #[Route('/{id}/pdf', name: 'app_employee_contract_pdf')]
public function pdf(Contract $contract): Response
{
    $user = $this->getUser();

    if ($contract->getEmploye()->getUser() !== $user) {
        throw $this->createAccessDeniedException();
    }

    $html = $this->renderView('employee/contracts/pdf.html.twig', [
        'contract' => $contract,
    ]);

    $options = new \Dompdf\Options();
    $options->set('defaultFont', 'Arial');

    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // 🔥 FIX HERE
    $dompdf->stream(
        'contract_'.$contract->getId().'.pdf',
        ["Attachment" => true]
    );

    exit(); // 💣 REQUIRED
}
}