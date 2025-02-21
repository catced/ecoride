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

        // if ($form->isSubmitted()) {
        //     dump($form->isValid());
        //     dump($form->getErrors(true));
        // }
        // dump($form->getData());
        if ($form->isSubmitted() && $form->isValid()) {    //$form->isSubmitted() &&
            // ); 
            // dump($form->getData());
                       // Vérifier si l'email existe déjà
            // $existingUser = $userRepository->findOneBy(['email' => $user->getEmail()]);
            // if ($existingUser) {
            //     $this->addFlash('error', 'Un compte existe déjà avec cette adresse email.');
            //     return $this->redirectToRoute('app_register');
            // }
            // $user->setPlainPassword($form->get('plainPassword')->getData());

            // $user->setPassword(
            //     $passwordHasher->hashPassword(
            //         $user,
            //         $user->getPlainPassword()
            //     )
            // );
          //  dump($form->getData());
            // Hachage du mot de passe
          
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $plainPassword = $form->get('plainPassword')->getData();
            // dump($plainPassword);
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
