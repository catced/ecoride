<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/userlogin.html.twig', [
        //return $this->render('security/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/userdashboard', name: 'app_userdashboard')]
    public function dashboard(): Response
    {
        return $this->render('user/userdashboard.html.twig');
    }


    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Ce contrÃ´leur ne sera jamais appelÃ© directement
    }

    #[Route('/user/login', name: 'user_login')]
    public function loginmembre(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/userlogin.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // #[Route('/membre/logout', name: 'membre_logout')]
    // public function logoutmembre(): void
    // {
    //     throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    // }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
{
    // Récupérer l'utilisateur connecté
    $user = $token->getUser();

    // Vérifier si l'utilisateur est un employé
    if (in_array('ROLE_EMPLOYE', $user->getRoles(), true)) {
        return $this->render('employe/dashboard.html.twig');
    }

    // Redirection par défaut (ex: admin ou autre rôle)
    //return new RedirectResponse($this->urlGenerator->generate('home'));
    return $this->render('user/userdashboard.html.twig');
}
}
