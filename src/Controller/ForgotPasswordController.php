<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\FormLoginAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class ForgotPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function request(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->addFlash('success', 'Si cette adresse e-mail existe, un code vous a été envoyé.');
            return $this->redirectToRoute('app_forgot_password_code');
        }

        return $this->render('auth/forgot_password.html.twig');
    }

    #[Route('/forgot-password/reset', name: 'app_forgot_password_code')]
    public function reset(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/forgot_password_code.html.twig');
    }

    #[Route('/send-otp', name: 'app_send_otp', methods: ['POST'])]
    public function sendOtp(Request $request, MailerInterface $mailer): Response
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return $this->json([
                'success' => false,
                'message' => 'Email manquant'
            ]);
        }

        $otp = random_int(100000, 999999);

        $session = $request->getSession();
        $session->set('otp_code', $otp);
        $session->set('otp_email', $email);

        $message = (new Email())
            ->from('boudour.zlaoui24@gmail.com')
            ->to($email)
            ->subject('Code OTP')
            ->text("Votre code OTP est : $otp");

        $mailer->send($message);

        return $this->json(['success' => true]);
    }

 #[Route('/verify-otp', name: 'app_verify_otp', methods: ['POST'])]
public function verifyOtp(
    Request $request,
    UserRepository $userRepository,
    TokenStorageInterface $tokenStorage
): Response {

    $data = json_decode($request->getContent(), true);
    $otp = $data['otp'] ?? null;

    $session = $request->getSession();

    $savedOtp = $session->get('otp_code');
    $email = $session->get('otp_email');

    if (!$otp || !$savedOtp || !$email) {
        return $this->json(['success' => false, 'message' => 'Session invalide']);
    }

    if ((int)$otp !== (int)$savedOtp) {
        return $this->json(['success' => false, 'message' => 'Code incorrect']);
    }

    $user = $userRepository->findOneBy(['email' => $email]);

    if (!$user) {
        return $this->json(['success' => false, 'message' => 'Utilisateur introuvable']);
    }

    // clean session OTP
    $session->remove('otp_code');
    $session->remove('otp_email');

    // 🔐 LOGIN SYMFONY RÉEL
    $token = new UsernamePasswordToken(
        $user,
        'main',
        $user->getRoles()
    );

    $tokenStorage->setToken($token);
    $session->set('_security_main', serialize($token));

    return $this->json([
        'success' => true,
        'message' => 'Connexion réussie'
    ]);
}


}
