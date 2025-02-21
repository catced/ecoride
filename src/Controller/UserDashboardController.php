<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Entity\User;
use App\Form\VehicleFormType;
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
        $vehicle = new Vehicle();
        //$vehicle->setOwner($this->getUser()); // Associe le véhicule à l'utilisateur connecté
        $vehicle->setOwner($this->getUser());
       
        $form = $this->createForm(VehicleFormType::class, $vehicle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($vehicle);
            $em->flush();
            $this->addFlash('success', 'Véhicule enregistré avec succès !');
            return $this->redirectToRoute('app_userdashboard'); // Recharge la page après soumission
        }

        return $this->render('user/userdashboard.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
