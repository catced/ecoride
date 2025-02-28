<?php

namespace App\Controller;

use App\Repository\RideRepository;
use App\Entity\Ride;
use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

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

//     #[Route('/search-routes', name: 'search_routes', methods: ['GET'])]
//     public function searchRoutes(Request $request, RideRepository $rideRepository): JsonResponse
// {
//     $query = $request->query->get('q', '');
    
//     // Effectuer une recherche en base de donnÃ¯Â¿Â½es
//     $rides = $rideRepository->findBySearchQuery($query);

//     // Formater les rÃ¯Â¿Â½sultats pour le JSON
//     $results = [];
//     foreach ($rides as $ride) {
//         $results[] = [
//             'id' => $ride->getId(),
//             'departure' => $ride->getDeparture(),
//             'destination' => $ride->getDestination(),
//             'date' => $ride->getDate()->format('d/m/Y H:i'),
//         ];
//     }

//     return $this->json($results);
// }

#[Route('/reserve/{id}', name: 'ride_book', methods: ['POST'])]
public function bookRide(int $id, Ride $ride, Request $request, RideRepository $rideRepository, EntityManagerInterface $em) : Response
{
    $ride = $rideRepository->findWithVehicle($id);

    if (!$ride) {
        throw $this->createNotFoundException("Trajet non trouvé !");
    }

    // RÃ©cupÃ©ration du vÃ©hicule associÃ© au trajet
    $vehicle = $ride->getVehicle();
    if (!$vehicle) {
        throw $this->createNotFoundException("Aucun véhicule associé à ce trajet.");
    }

    // RÃ©cupÃ©ration du nombre de places demandÃ©es
    $seatsRequested = (int) $request->request->get('seats', 1);

    $user = $this->getUser();
    if (!$user) {
        $this->addFlash('error', "Vous devez être connecté pour réserver.");
        return $this->redirectToRoute('app_login');
    }


    // VÃ©rification du nombre de places disponibles
    if ($seatsRequested > 0 && $ride->getAvailableSeats() >= $seatsRequested) {
        // Mise Ã  jour des places disponibles
        $ride->setAvailableSeats($ride->getAvailableSeats() - $seatsRequested);

        $booking = new Booking();
        $booking->setRide($ride);
        $booking->setUser($user);
        $booking->setSeatsBooked($seatsRequested);

        dump($booking);
       
        $em->persist($booking);
        $em->persist($ride);
        $em->flush();
      
        $this->addFlash('success', "Vous avez réservé $seatsRequested place(s) pour le trajet du " . 
            $ride->getDepartureDay()->format('d/m/Y Ã  H:i') . " de " . 
            $ride->getDeparture() . " Ã  " . $ride->getDestination() . ".");
    } else {
        $this->addFlash('error', "Désolé, il n'y a pas assez de places disponibles.");
    }

    return $this->redirectToRoute('ride_details', ['id' => $ride->getId()]);
}

    // if ($ride->getSeatsCount() > 0) {
    //     // On rÃ©serve une place
    //     $ride->setSeatsCount($ride->getSeatsCount() - 1);

    //     // Sauvegarde de la mise Ã  jour dans la base de donnÃ©es
    //     $entityManager = $this->getDoctrine()->getManager();
    //     $entityManager->flush();

    //     // Message de confirmation
    //     $this->addFlash('success', 'Vous avez rÃ©servÃ© une place pour le trajet du ' . $ride->getDepartureDay()->format('d/m/Y Ã  H:i') . ' de ' . $ride->getDeparture() . ' Ã  ' . $ride->getDestination());
    // } else {
    //     // Message si aucune place disponible
    //     $this->addFlash('error', 'DÃ©solÃ©, il n\'y a plus de place disponible pour ce trajet.');
    // }

    // Redirection vers la page de dÃ©tails du trajet

}
