<?php
namespace App\EventListener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\GenericEvent;

class LoginRedirectListener
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        
        // Si l'utilisateur a le rôle _admin
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            // Redirection vers la page admin
            $url = $this->router->generate('admin'); // Assurez-vous que 'admin_dashboard' est la route de votre page d'administration
        } 
        // Si l'utilisateur a le rôle _employe
        elseif (in_array('ROLE_EMPLOYE', $user->getRoles())) {
            // Redirection vers la page Employé/dashboard
            $url = $this->router->generate('employe_dashboard'); // Assurez-vous que 'employe_dashboard' est la route de votre page Employé
        } 
        // Si l'utilisateur a le rôle _user
        elseif (in_array('ROLE_USER', $user->getRoles())) {
            // Redirection vers la page search_results
            $url = $this->router->generate('search_results'); // Assurez-vous que 'search_results' est la route de votre page de résultats de recherche
        } else {
            // Par défaut, redirection vers une page par défaut ou l'accueil
            $url = $this->router->generate('homepage');
        }

        // Effectuer la redirection
        $response = new RedirectResponse($url);
       // $event->setResponse($response);
    }
}
