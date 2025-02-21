<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\Vehicle;
use App\Entity\User;
use App\Form\VehicleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;


class VehicleController extends AbstractController
{
#[Route('/ajouter-vehicule', name: 'ajouter_vehicule')]
public function ajouterVehicule(Request $request, EntityManagerInterface $entityManager): Response
{
    $vehicle = new Vehicle();
    $vehicle->setOwner($this->getUser());

    $form = $this->createForm(VehicleFormType::class, $vehicle);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        // $preferences = $form->get('preferences')->getData();
            
        // // Récupérer les préférences personnalisées
        // $customPreferences = $form->get('customPreferences')->getData();

        // // Fusionner les préférences en supprimant les champs vides
        // $allPreferences = array_merge($preferences, array_filter($customPreferences));

        // // Enregistrer dans l'entité Vehicle
        // $vehicle->setPreferences($allPreferences);
        
        $entityManager->persist($vehicle);
        $entityManager->flush();

        return $this->redirectToRoute('app_userdashboard');
    }

    return $this->render('vehicle/ajouter.html.twig', [
        'form' => $form->createView(),
    ]);
}



}