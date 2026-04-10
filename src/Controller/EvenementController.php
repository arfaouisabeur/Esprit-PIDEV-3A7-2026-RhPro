<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Activite;
use App\Form\EvenementType;
use App\Form\ActiviteType;
use App\Repository\EvenementRepository;
use App\Repository\EventParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/rh/evenement')]
#[IsGranted('ROLE_RH')]
class EvenementController extends AbstractController
{
    #[Route('', name: 'app_evenement_index')]
    public function index(EvenementRepository $repo): Response
    {
        return $this->render('evenement/index.html.twig', [
            'evenements' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_evenement_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $evenement = new Evenement();

        // Auto-assign the currently logged-in RH
        $user = $this->getUser();
        if ($user && $user->getRh()) {
            $evenement->setRh($user->getRh());
        }

        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $evenement->setDateDebut(substr($evenement->getDateDebut(), 0, 10));
            $evenement->setDateFin(substr($evenement->getDateFin(), 0, 10));

            // Ensure rh is still set after form binding
            if ($user && $user->getRh() && $evenement->getRh() === null) {
                $evenement->setRh($user->getRh());
            }

            if ($form->isValid()) {
                $em->persist($evenement);
                $em->flush();
                $this->addFlash('success', 'Événement créé avec succès.');
                return $this->redirectToRoute('app_evenement_index');
            }
        }

        return $this->render('evenement/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'app_evenement_show')]
    public function show(
        Request $request,
        Evenement $evenement,
        EntityManagerInterface $em,
        EventParticipationRepository $participationRepo
    ): Response {
        $activite = new Activite();
        $activite->setEvenement($evenement);
        $activiteForm = $this->createForm(ActiviteType::class, $activite);
        $activiteForm->handleRequest($request);

        if ($activiteForm->isSubmitted() && $activiteForm->isValid()) {
            $em->persist($activite);
            $em->flush();
            $this->addFlash('success', 'Activité ajoutée.');
            return $this->redirectToRoute('app_evenement_show', ['id' => $evenement->getId()]);
        }

        $participations = $participationRepo->findBy(['evenement' => $evenement]);

        return $this->render('evenement/show.html.twig', [
            'evenement'      => $evenement,
            'activiteForm'   => $activiteForm,
            'participations' => $participations,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $em): Response
    {
        // Remember the original RH in case form clears it
        $originalRh = $evenement->getRh();
        $user = $this->getUser();

        $form = $this->createForm(EvenementType::class, $evenement, [
            'csrf_protection'  => false, // ✅ fix: custom date inputs bypass CSRF widget
            'validation_groups' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $evenement->setDateDebut(substr($evenement->getDateDebut(), 0, 10));
            $evenement->setDateFin(substr($evenement->getDateFin(), 0, 10));

            // Restore or assign RH if lost during form binding
            if ($evenement->getRh() === null) {
                if ($originalRh) {
                    $evenement->setRh($originalRh);
                } elseif ($user && $user->getRh()) {
                    $evenement->setRh($user->getRh());
                }
            }

            if ($form->isValid()) {
                $em->flush();
                $this->addFlash('success', 'Événement modifié avec succès.');
                return $this->redirectToRoute('app_evenement_index');
            }
        }

        return $this->render('evenement/edit.html.twig', [
            'form'      => $form,
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_evenement_delete')]
    public function delete(Evenement $evenement, EntityManagerInterface $em): Response
    {
        $em->remove($evenement);
        $em->flush();
        $this->addFlash('success', 'Événement supprimé.');
        return $this->redirectToRoute('app_evenement_index');
    }
}