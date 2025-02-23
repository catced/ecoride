<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Entity\Ride;
use App\Entity\User;
use App\Form\VehicleFormType;
use App\Form\RideFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserDashboardController extends AbstractController
{
    #[Route('/userdashboard', name: 'app_userdashboard')]
    public function dashboard(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $vehicles = $em->getRepository(Vehicle::class)->findBy(['owner' => $user]);
         /*** FORMULAIRE D'AJOUT DE V�HICULE ***/
         $vehicle = new Vehicle();
         $vehicle->setOwner($user);
         $vehicleForm = $this->createForm(VehicleFormType::class, $vehicle);
         $vehicleForm->handleRequest($request);
 
         if ($vehicleForm->isSubmitted() && $vehicleForm->isValid()) {
             $em->persist($vehicle);
             $em->flush();
             $this->addFlash('success', 'V�hicule enregistr� avec succ�s !');
             return $this->redirectToRoute('app_userdashboard');
         }
 
         /*** FORMULAIRE D'AJOUT DE TRAJET ***/
         $ride = new Ride();
         $ride->setDriver($user);
         $rideForm = $this->createForm(RideFormType::class, $ride, [
             'vehicles' => $vehicles, // Passe les v�hicules � RideFormType
         ]);
         $rideForm->handleRequest($request);
 
         if ($rideForm->isSubmitted() && $rideForm->isValid()) {
             if ($user->getCredit() < 2) {
                 $this->addFlash('error', 'Vous n\'avez pas assez de cr�dits pour proposer un voyage.');
             } else {
                 // D�duire 2 cr�dits et enregistrer le trajet
                 $user->setCredit($user->getCredit() - 2);
                 $em->persist($ride);
                 $em->flush();
                 $this->addFlash('success', 'Trajet propos� avec succ�s !');
                 return $this->redirectToRoute('app_userdashboard');
             }
         }
 
         return $this->render('user/userdashboard.html.twig', [
             'vehicleForm' => $vehicleForm->createView(),
             'rideForm' => $rideForm->createView(),
             'vehicles' => $vehicles,
         ]);
     }
}
