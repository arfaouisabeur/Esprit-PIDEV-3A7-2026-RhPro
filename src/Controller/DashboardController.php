<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileCandidatType;
use App\Form\ProfileEmployeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_home')]
    public function dashboard(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($user->isRH()) {
            return $this->redirectToRoute('app_rh_dashboard');
        }

        if ($user->isCandidat()) {
            return $this->redirectToRoute('app_candidat_dashboard');
        }

        if ($user->isEmploye()) {
            return $this->redirectToRoute('app_employe_dashboard');
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/candidat/dashboard', name: 'app_candidat_dashboard')]
    #[IsGranted('ROLE_CANDIDAT')]
    public function candidatDashboard(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $candidat = $user->getCandidat();

        return $this->render('dashboard/candidat_dashboard.html.twig', [
            'user' => $user,
            'candidat' => $candidat,
        ]);
    }

    #[Route('/candidat/profile', name: 'app_candidat_profile')]
    #[IsGranted('ROLE_CANDIDAT')]
    public function candidatProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $candidat = $user->getCandidat();

        $form = $this->createForm(ProfileCandidatType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if email already exists (except current user)
            $newEmail = $form->get('email')->getData();
            if ($newEmail !== $user->getEmail()) {
                $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $newEmail]);
                if ($existingUser) {
                    $this->addFlash('error', 'Cet email est déjà utilisé par un autre utilisateur.');
                    return $this->redirectToRoute('app_candidat_profile');
                }
            }

            // Check if telephone already exists (except current user)
            $newTelephone = $form->get('telephone')->getData();
            if ($newTelephone !== $user->getTelephone()) {
                $existingPhone = $entityManager->getRepository(User::class)->findOneBy(['telephone' => $newTelephone]);
                if ($existingPhone && $existingPhone->getId() !== $user->getId()) {
                    $this->addFlash('error', 'Ce numéro de téléphone est déjà utilisé.');
                    return $this->redirectToRoute('app_candidat_profile');
                }
            }

            // Update candidat info
            if ($candidat) {
                $candidat->setNiveauEtude($form->get('niveauEtude')->getData());
                $candidat->setExperience($form->get('experience')->getData());
            }

            // Handle avatar upload
            $avatarFile = $form->get('avatar')->getData();
            if ($avatarFile) {
                $newFilename = 'avatar_user_' . $user->getId() . '.' . $avatarFile->guessExtension();
                $avatarFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/avatars',
                    $newFilename
                );
                $user->setAvatarPath('uploads/avatars/' . $newFilename);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');

            return $this->redirectToRoute('app_candidat_profile');
        }

        // Set candidat data in form
        if ($candidat) {
            $form->get('niveauEtude')->setData($candidat->getNiveauEtude());
            $form->get('experience')->setData($candidat->getExperience());
        }

        return $this->render('dashboard/candidat_profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/employe/dashboard', name: 'app_employe_dashboard')]
    #[IsGranted('ROLE_EMPLOYE')]
    public function employeDashboard(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $employe = $user->getEmploye();

        return $this->render('dashboard/employe_dashboard.html.twig', [
            'user' => $user,
            'employe' => $employe,
        ]);
    }

    #[Route('/employe/profile', name: 'app_employe_profile')]
    #[IsGranted('ROLE_EMPLOYE')]
    public function employeProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $employe = $user->getEmploye();

        $form = $this->createForm(ProfileEmployeType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if email already exists (except current user)
            $newEmail = $form->get('email')->getData();
            if ($newEmail !== $user->getEmail()) {
                $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $newEmail]);
                if ($existingUser) {
                    $this->addFlash('error', 'Cet email est déjà utilisé par un autre utilisateur.');
                    return $this->redirectToRoute('app_employe_profile');
                }
            }

            // Check if telephone already exists (except current user)
            $newTelephone = $form->get('telephone')->getData();
            if ($newTelephone !== $user->getTelephone()) {
                $existingPhone = $entityManager->getRepository(User::class)->findOneBy(['telephone' => $newTelephone]);
                if ($existingPhone && $existingPhone->getId() !== $user->getId()) {
                    $this->addFlash('error', 'Ce numéro de téléphone est déjà utilisé.');
                    return $this->redirectToRoute('app_employe_profile');
                }
            }

            // Update employe info
            if ($employe) {
                $employe->setPosition($form->get('position')->getData());
                $employe->setDateEmbauche($form->get('dateEmbauche')->getData());
            }

            // Handle avatar upload
            $avatarFile = $form->get('avatar')->getData();
            if ($avatarFile) {
                $newFilename = 'avatar_user_' . $user->getId() . '.' . $avatarFile->guessExtension();
                $avatarFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/avatars',
                    $newFilename
                );
                $user->setAvatarPath('uploads/avatars/' . $newFilename);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');

            return $this->redirectToRoute('app_employe_profile');
        }

        // Set employe data in form
        if ($employe) {
            $form->get('position')->setData($employe->getPosition());
            $form->get('dateEmbauche')->setData($employe->getDateEmbauche());
        }

        return $this->render('dashboard/employe_profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/delete-account', name: 'app_delete_account', methods: ['POST'])]
    public function deleteAccount(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Delete avatar file if exists
        if ($user->getAvatarPath()) {
            $avatarPath = $this->getParameter('kernel.project_dir') . '/public/' . $user->getAvatarPath();
            if (file_exists($avatarPath)) {
                unlink($avatarPath);
            }
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a été supprimé avec succès.');
        return $this->redirectToRoute('app_home');
    }
}
