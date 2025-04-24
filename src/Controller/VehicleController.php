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
#[Route('/add-vehicle', name: 'add_vehicle')]
public function ajouterVehicule(Request $request, EntityManagerInterface $entityManager): Response
{
    $vehicle = new Vehicle();
    $vehicle->setOwner($this->getUser());

    $form = $this->createForm(VehicleFormType::class, $vehicle);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $entityManager->persist($vehicle);
        $entityManager->flush();

        return $this->redirectToRoute('app_userdashboard', ['role' => 'chauffeur']);
    }

    return $this->render('vehicle/add_vehicle.html.twig', [
        'form' => $form->createView(),
    ]);
  
}



}