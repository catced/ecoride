<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RideRepository;
use App\Entity\Ride;
use App\Form\RideFilterFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\VarDumper\VarDumper; 

class Search_ResultsController extends AbstractController
{
    
    // #[Route("/search-results", name:"search_results")]
    // public function searchResults(Request $request): Response
    // {
    //     $query = $request->query->get('q');

    //     return $this->render('search/results.html.twig', [
    //         'query' => $query,
    //     ]);
    // }
//     #[Route('/search-result', name: 'search_results', methods: ['GET'])]
//     public function searchRoutes(Request $request, RideRepository $rideRepository): JsonResponse
// {
//     $query = $request->query->get('q', '');
    
//     // Effectuer une recherche en base de donn?es
//     $rides = $rideRepository->findBySearchQuery($query);

//     // Formater les r?sultats pour le JSON
//     $results = [];
//     foreach ($rides as $ride) {
//         $results[] = [
//             'id' => $ride->getId(),
//             'departure' => $ride->getDeparture(),
//             'destination' => $ride->getDestination(),
//             'date' => $ride->getDepartureDay()->format('d/m/Y H:i'),
//         ];
//     }

//     return $this->json($results);
// }
    // src/Controller/SearchResultsController.php

#[Route('/search-result', name: 'search_results')]
public function search(Request $request, RideRepository $rideRepository, EntityManagerInterface $em): Response
{
    // Récupérer les valeurs du formulaire
    // $departure = $request->query->get('departure');
    // $destination = $request->query->get('destination');
    // $departureDay = $request->query->get('departureDay');

    $form = $this->createForm(RideFilterFormType::class);

    // Traitez le formulaire
    $form->handleRequest($request);

    // Construisez la requête de filtrage
    // $ridesQuery = $em->getRepository(Ride::class)->createQueryBuilder('r');

    // // Appliquez les filtres si les valeurs sont définies
    // if ($form->isSubmitted() && $form->isValid()) {
       // $data = $form->getData();

        
    // // Construire la requête dynamique
   

    //     if ($data->getDeparture()) {
    //         $ridesQuery->andWhere('r.departure = :departure')
    //                     ->setParameter('departure', '%'.$data->getDeparture().'%');
                        
    //     }
    //     if ($data->getDestination()) {
    //         $ridesQuery->andWhere('r.destination = :destination')
    //                     ->setParameter('destination', '%'.$data->getDestination().'%');
                        
    //     }
    // }
            // if ($data->getDepartureDay()) {
            //     $departureDay = \DateTime::createFromFormat('d/m/Y', $data->getDepartureDay());
            //     $ridesQuery->andWhere('r.departureDay >= :departureDay')
            //                  ->setParameter('departureDay', $data->getDepartureDay());
            // }
            // }
        
        
    // $rides = $ridesQuery->getQuery()->getResult();

    $departure = $request->query->get('departure');
    $destination = $request->query->get('destination');
    //$today = new \DateTimeImmutable(); 
    $departureDay =  $request->query->get('departureDay');
    //$energy = $request->query->get('energy');
    $price = $request->query->get('price');
    $availableSeats = $request->query->get('availableSeats');
    $duration = $request->query->get('duration');


    if ($departureDay) {
        $departureDay = \DateTime::createFromFormat('d/m/Y', $departureDay);
        if ($departureDay) {
            $departureDay->setTime(0, 0, 0); // Met l'heure � minuit pour ignorer l'heure
        }
    }
    
 
    $ridesQuery = $em->createQueryBuilder()
    ->select('r')
    ->from('App\Entity\Ride', 'r')
    ->where('r.departure = :departure')
    ->andWhere('r.destination = :destination')
    ->andWhere('r.availableSeats >= 1')
    ->andWhere('r.departureDay >= :departureDay')
    ->setParameter('departure', $departure)
    ->setParameter('destination', $destination)
    ->setParameter('departureDay', $departureDay);
    if ($price) {
        $ridesQuery->andWhere('r.price <= :price')
                   ->setParameter('price', $price);
    }
    if ($availableSeats) {
        $ridesQuery->andWhere('r.availableSeats >= :availableSeats')
                   ->setParameter('availableSeats', $availableSeats);
    }
    if ($duration) {
        $ridesQuery->andWhere('r.duration <= :duration')
                   ->setParameter('duration', $duration);
    }
    
    // if ($availableSeats) {
    //     $ridesQuery->andWhere('r.availableSeats <= :availableSeats')
    //                ->setParameter('availableSeats', $availableSeats);
    // }
    // if ($departureDay) {
    //     $ridesQuery->setParameter('departureDay', $departureDay);
    // }

    $rides = $ridesQuery->getQuery()->getResult();
    


        return $this->render('ride/search_results.html.twig', [
            'form' => $form->createView(),
            'rides' => $rides,
        ]);
    }

}