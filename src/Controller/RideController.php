<?php

namespace App\Controller;

use App\Repository\RideRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RideController extends AbstractController
{
    #[Route('/rides', name: 'ride_list')]
    public function list(RideRepository $rideRepository): Response
    {
        $rides = $rideRepository->findAll();
        return $this->render('ride/list.html.twig', [
            'rides' => $rides,
        ]);
    }

    #[Route('/ride/{id}', name: 'ride_details')]
    public function details($id, RideRepository $rideRepository): Response
    {
        $ride = $rideRepository->find($id);
        if (!$ride) {
            throw $this->createNotFoundException("Trajet non trouvé !");
        }

        return $this->render('ride/details.html.twig', [
            'ride' => $ride,
        ]);
    }

    #[Route('/search-routes', name: 'search_routes', methods: ['GET'])]
    public function searchRoutes(Request $request, RideRepository $rideRepository): JsonResponse
{
    $query = $request->query->get('q', '');
    
    // Effectuer une recherche en base de données
    $rides = $rideRepository->findBySearchQuery($query);

    // Formater les résultats pour le JSON
    $results = [];
    foreach ($rides as $ride) {
        $results[] = [
            'id' => $ride->getId(),
            'departure' => $ride->getDeparture(),
            'destination' => $ride->getDestination(),
            'date' => $ride->getDate()->format('d/m/Y H:i'),
        ];
    }

    return $this->json($results);
}
}
