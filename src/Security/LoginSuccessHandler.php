<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    // private RouterInterface $router;
    // private Security $security;

    // public function __construct(RouterInterface $router, Security $security)
    // {
    //     $this->router = $router;
    //     $this->security = $security;
    // }

    // public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    // {
    //     // Obtenir l'utilisateur connecté
    //     $user = $token->getUser();

    //     if ($this->security->isGranted('ROLE_ADMIN')) {
    //         // Rediriger les administrateurs vers l'espace admin
    //         return new RedirectResponse($this->router->generate('admin_dashboard'));
    //     }

    //     // Rediriger les membres vers la page générale
    //     return new RedirectResponse($this->router->generate('app_home'));
    // }
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        // Récupérer l'utilisateur connecté
        $user = $token->getUser();

        // Vérifier si l'utilisateur a le rôle d'admin
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            // Redirection vers la page admin
            return new RedirectResponse($this->router->generate('admin'));
        }

        // Redirection vers la page d'accueil (ou une autre) si l'utilisateur n'est pas admin
        if (in_array('ROLE_USER', $user->getRoles())) {
            return new RedirectResponse($this->router->generate('membre_dashboard'));
        }
        return new RedirectResponse($this->router->generate('accueil'));
    }
}
