<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Candidat;
use App\Entity\Employe;
use App\Form\RegistrationCandidatType;
use App\Form\RegistrationEmployeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/register/candidat', name: 'app_register_candidat')]
    public function registerCandidat(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationCandidatType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $existingUser = $entityManager->getRepository(User::class)->findOneBy([
                'email' => $user->getEmail()
            ]);

            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->render('auth/register_candidat.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $user->setRole(User::ROLE_CANDIDAT);
            $user->setStatut('actif');

            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setMotDePasse($hashedPassword);

            $avatarFile = $form->get('avatar')->getData();

            if ($avatarFile) {
                $newFilename = 'avatar_user_temp_' . uniqid() . '.' . $avatarFile->guessExtension();
                $avatarFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/avatars',
                    $newFilename
                );
                $user->setAvatarPath('uploads/avatars/' . $newFilename);
            }

            $candidat = new Candidat();
            $candidat->setNiveauEtude($form->get('niveauEtude')->getData() ?? '');
            $candidat->setExperience($form->get('experience')->getData() ?? 0);

            $entityManager->persist($user);
            $entityManager->flush();

            if ($user->getAvatarPath() && str_contains($user->getAvatarPath(), 'temp')) {
                $oldPath = $this->getParameter('kernel.project_dir') . '/public/' . $user->getAvatarPath();
                $newFilename = 'avatar_user_' . $user->getId() . '.' . pathinfo($oldPath, PATHINFO_EXTENSION);
                $newPath = $this->getParameter('kernel.project_dir') . '/public/uploads/avatars/' . $newFilename;

                if (file_exists($oldPath)) {
                    rename($oldPath, $newPath);
                    $user->setAvatarPath('uploads/avatars/' . $newFilename);
                    $entityManager->persist($user);
                }
            }

            $candidat->setUser($user);
            $entityManager->persist($candidat);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/register_candidat.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/register/employe', name: 'app_register_employe')]
    public function registerEmploye(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationEmployeType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $existingUser = $entityManager->getRepository(User::class)->findOneBy([
                'email' => $user->getEmail()
            ]);

            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->render('auth/register_employe.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $existingMatricule = $entityManager->getRepository(Employe::class)->findOneBy([
                'matricule' => $form->get('matricule')->getData()
            ]);

            if ($existingMatricule) {
                $this->addFlash('error', 'Ce matricule est déjà utilisé.');
                return $this->render('auth/register_employe.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $user->setRole(User::ROLE_EMPLOYE);
            $user->setStatut('actif');

            // plainPassword est forcément non-null ici car isValid() = true
            // et NotBlank est défini dans le FormType
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setMotDePasse($hashedPassword);

            $avatarFile = $form->get('avatar')->getData();

            if ($avatarFile) {
                $newFilename = 'avatar_user_temp_' . uniqid() . '.' . $avatarFile->guessExtension();
                $avatarFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/avatars',
                    $newFilename
                );
                $user->setAvatarPath('uploads/avatars/' . $newFilename);
            }

            $employe = new Employe();
            $employe->setMatricule($form->get('matricule')->getData());
            $employe->setPosition($form->get('position')->getData());
            $employe->setDateEmbauche($form->get('dateEmbauche')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            if ($user->getAvatarPath() && str_contains($user->getAvatarPath(), 'temp')) {
                $oldPath = $this->getParameter('kernel.project_dir') . '/public/' . $user->getAvatarPath();
                $newFilename = 'avatar_user_' . $user->getId() . '.' . pathinfo($oldPath, PATHINFO_EXTENSION);
                $newPath = $this->getParameter('kernel.project_dir') . '/public/uploads/avatars/' . $newFilename;

                if (file_exists($oldPath)) {
                    rename($oldPath, $newPath);
                    $user->setAvatarPath('uploads/avatars/' . $newFilename);
                    $entityManager->persist($user);
                }
            }

            $employe->setUser($user);
            $entityManager->persist($employe);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/register_employe.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void {}
}