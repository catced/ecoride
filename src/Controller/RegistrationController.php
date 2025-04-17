<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\UserRegistrationFormType;
use App\Entity\User;
use App\Repository\UserRepository;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserRegistrationFormType::class, $user);
    
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {    //$form->isSubmitted() &&
                      
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $plainPassword = $form->get('plainPassword')->getData();

            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $plainPassword)) {
                $this->addFlash('danger', 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.');
                return $this->redirectToRoute('app_login');
            }

            if (!empty($plainPassword)) { // Vérifie si un mot de passe a été saisi
              
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
                $user->eraseCredentials(); // Supprime les données sensibles en mémoire
            }

            if (empty($user->getRoles())) {
                $user->setRoles(['ROLE_USER']);
                $user->setCredit(20);
            }

            // if ($user->getUserType() === 'C' || $user->getUserType() === 'CP') {
            //     // $vehiclesData = $form->get('vehicles')->getData();
            //     // foreach ($vehiclesData as $vehicle) {
            //     //     $user->addVehicle($vehicle); // Associe le véhicule à l'utilisateur
            //     // }
            //     foreach ($user->getVehicles() as $vehicle) {
            //         $vehicle->setOwner($user); // Associe chaque véhicule au User
            //         $entityManager->persist($vehicle);
            //     }
            
            // }

            // Sauvegarde en base de données
            $entityManager->persist($user);
            $entityManager->flush();
          

            $this->addFlash('success', 'Votre compte a été créé avec succès ! Connectez-vous maintenant.');

            return $this->redirectToRoute('app_login');
        }


       
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
