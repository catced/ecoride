<?php

namespace App\Controller;

use App\Repository\VoyageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class VoyageController extends AbstractController
{
    #[Route('/mes-voyages', name: 'app_mes_voyages')]
    #[IsGranted('ROLE_USER')]
    public function mesVoyages(VoyageRepository $voyageRepository): Response
    {
        $user = $this->getUser();
        
        // Vérifier si l'utilisateur est passager
        //if (!$user->isPassager()) {
        // if (!$user->isPassager()) {
        //     throw $this->createAccessDeniedException('Accès refusé.');
        // }

        $dateDuJour = new \DateTime();

        // Récupération des voyages de l'utilisateur
        $voyagesPasses = $voyageRepository->findByVoyagesPasses($user, $dateDuJour);
        $voyagesAVenir = $voyageRepository->findByVoyagesAVenir($user, $dateDuJour);

        return $this->render('User/voyages.html.twig', [
            'voyagespasses' => $voyagesPasses ?? [],
            'voyagesavenir' => $voyagesAVenir ?? [],
        ]);
    }
}
