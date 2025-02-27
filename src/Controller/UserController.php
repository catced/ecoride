<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\entity\UserRepository;
use App\Form\UserRegistrationFormType;
use Symfony\Component\Form\FormFactoryInterface;
use App\Form\VehicleFormType; // Assurez-vous que cette classe existe bien
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Booking;



class UserController extends AbstractController
{
    // #[Route('/login', name: 'app_login')]
    // public function login(AuthenticationUtils $authenticationUtils): Response
    // {
    //     return $this->render('user/login.html.twig', [
    //         'error' => $authenticationUtils->getLastAuthenticationError(),
    //         'last_username' => $authenticationUtils->getLastUsername(),
    //     ]);
    // }

    // #[Route('/profile', name: 'user_profile')]
    // public function profile(): Response
    // {
    //     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    //     return $this->render('user/profile.html.twig');
    // }
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();

        if ($user) {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->redirectToRoute('admin_dashboard');
            } else {
                return $this->redirectToRoute('user_profile');
            }
        }

        return $this->render('user/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
        ]);
    }

    // #[Route('/choix-role', name: 'user_role_choice')]
    // public function choixRole(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $user = $this->getUser();
    //     $form = $this->createForm(UserRegistrationFormType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($user);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('user_dashboard');
    //     }

    //     return $this->render('user/choix_role.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }
  
    #[Route('/set-role', name:'set_role')]   //, methods:{"POST"}
    public function setRole(Request $request, SessionInterface $session):response
    {
        // Récupérer le rôle envoyé dans le formulaire
        $role = $request->request->get('role');
        dump($role);
        // Sauvegarder le rôle dans la session
        $session->set('user_role', $role);

        // Rediriger l'utilisateur après avoir sauvé le rôle
        return $this->redirectToRoute('user_dashboard');
    }

  
    // #[Route('/user-dashboard', name:'user_dashboard')]
    // public function userDashboard(SessionInterface $session)
    // {
    //     // Récupérer le rôle depuis la session
    //     $role = $session->get('user_role', 'passager'); // Valeur par défaut si aucun rôle n'est stocké

    //     return $this->render('user/userdashboard.html.twig', [
    //         'role' => $role,
    //     ]);
    // }

    #[Route('/user-dashboard', name:'user_dashboard')]
    public function userDashboard(SessionInterface $session, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, Request $request)
    {
        $role = $session->get('user_role', 'passager');

        // Création du formulaire (assurez-vous d'avoir un `VehicleType`)
        $vehicleForm = $formFactory->create(VehicleFormType::class);
        $vehicleForm->handleRequest($request);

        if ($vehicleForm->isSubmitted() && $vehicleForm->isValid()) {
            $vehicle = $vehicleForm->getData();
            $entityManager->persist($vehicle);
            $entityManager->flush();
        }

        return $this->render('user/userdashboard.html.twig', [
            'role' => $role,
            'vehicleForm' => $vehicleForm->createView(), // ?? On envoie le formulaire à Twig
        ]);
    }

    #[Route('/passager-dashboard', name:'passager_dashboard')]
    public function passagerDashboard(SessionInterface $session)
    {
        // Récupérer le rôle depuis la session
        $role = $session->get('user_role', 'passager'); // Valeur par défaut si aucun rôle n'est stocké

        return $this->render('ride/search_results.html.twig', [
            'role' => $role,
        ]);
    }

    #[Route('/mes-reservations', name: 'user_bookings')]
    public function userBookings(): Response
    {
        $user = $this->getUser();
        $bookings = $this->getDoctrine()->getRepository(Booking::class)->findBy(['user' => $user]);

        return $this->render('booking/user_bookings.html.twig', [
            'bookings' => $bookings,
        ]);
}
}
