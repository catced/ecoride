<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
//use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;



class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, RouterInterface $router, Security $security)
    {
        

      //  dump('Début de la méthode login');
        
        $user = $security->getUser();
        // dump($user); // Affiche l'utilisateur connecté ou null
        // dump($user->getRoles());
        // exit;
        
        if ($user) {
          
           

            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                // dump('Redirection vers admin_dashboard');
                // exit;
                return $this->redirectToRoute('admin');
            } elseif (in_array('ROLE_EMPLOYE', $user->getRoles())) {
                return $this->redirectToRoute('employe_dashboard');
            } elseif (in_array('ROLE_USER', $user->getRoles())) {
                return $this->redirectToRoute('search_results');
            } else {
                return $this->redirectToRoute('homepage');
            }

            // dump('Redirection vers app_home');
            // exit;
            
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        
    //    dump('Affichage du formulaire de connexion');
    //    exit; 
        return $this->render('security/userlogin.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    
   

    // public function login(AuthenticationUtils $authenticationUtils, RouterInterface $router): Response
    // {
    //     // Récupérer les erreurs d'authentification
    //     $error = $authenticationUtils->getLastAuthenticationError();

    //     // Récupérer l'utilisateur actuellement authentifié
    //     $user = $this->getUser();

    //     // Vérifiez que l'utilisateur est connecté et effectuer la redirection
    //     if ($user) {
    //         if (in_array('ROLE_ADMIN', $user->getRoles())) {
    //             return $this->redirectToRoute('admin');
    //         } elseif (in_array('ROLE_EMPLOYE', $user->getRoles())) {
    //             return $this->redirectToRoute('employe_dashboard');
    //         } elseif (in_array('ROLE_USER', $user->getRoles())) {
    //             return $this->redirectToRoute('search_results');
    //         }
    //     }

    //     // Si l'utilisateur n'est pas encore authentifié, afficher la page de connexion
    //     return $this->render('security/login.html.twig', [
    //         'error' => $error,
    //     ]);
    // }

    // #[Route('/redirect', name: 'redirect_after_login')]
    // public function redirectAfterLogin(Security $security): Response
    // {
    //     $user = $security->getUser();

    //     if (!$user) {
    //         return $this->redirectToRoute('app_login');
    //     }

    //     // Vérification des rôles
    //     if ($this->isGranted('ROLE_ADMIN')) {
    //         return $this->redirectToRoute('admin'); // Redirige vers l'admin
    //     } elseif ($this->isGranted('ROLE_EMPLOYE')) {
    //         return $this->redirectToRoute('user_dashboard'); // Redirige vers l'utilisateur
    //     } else {
    //         return $this->redirectToRoute('default_dashboard'); // Redirige vers une autre page
    //     }
    // }

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

    // #[Route('/user/login', name: 'user_login')]
    // public function loginmembre(AuthenticationUtils $authenticationUtils): Response
    // {
    //     $error = $authenticationUtils->getLastAuthenticationError();
    //     $lastUsername = $authenticationUtils->getLastUsername();

    //     return $this->render('security/userlogin.html.twig', [
    //         'last_username' => $lastUsername,
    //         'error' => $error,
    //     ]);
    // }

    // #[Route('/membre/logout', name: 'membre_logout')]
    // public function logoutmembre(): void
    // {
    //     throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    // }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
        {
            // Redirection par défaut (ex: admin ou autre rôle)
            //return new RedirectResponse($this->urlGenerator->generate('home'));
            $user = $token->getUser();

            if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                // Redirection vers la page admin
                return $this->redirectToRoute('admin');
            }
            // V?fier si l'utilisateur est un employ?    
            if (in_array('ROLE_EMPLOYE', $user->getRoles(), true)) {
                return $this->render('employe/dashboard.html.twig');
            }

            return $this->render('user/userdashboard.html.twig');
            
        }
}
