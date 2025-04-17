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
use App\Repository\BookingRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\Search_ResultsRepository;



class Search_ResultsController1 extends AbstractController
{
    
    // #[Route("/search-results1", name:"search_results")]
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

#[Route('/search_results', name: 'search_results', methods: ['GET'])]
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
    $departureDayString =  $request->query->get('departureDay');
    // if ($departureDayString) {
    //     $departureDay = \DateTime::createFromFormat('d/m/Y', $departureDayString);
    //     if (!$departureDay) {
    //         throw new \Exception("Format de date invalide !");
    //     }
    // } else {
    //     $departureDay = new \DateTime(); // Prendre la date du jour si aucune date fournie
    // }
    if ($departureDayString) {
        $departureDay = \DateTime::createFromFormat('d/m/Y', $departureDayString);
        
        if (!$departureDay) {
            throw new \Exception("Format de date invalide !");
        } 
    
        // Conversion au format compatible avec la base de donn�es
        $departureDayFormatted = $departureDay->format('Y-m-d');
    } else {
        $departureDayFormatted = null; // Pas de filtre si pas de date fournie
    }
    //$energy = $request->query->get('energy');
    $price = $request->query->get('price');
    $availableSeats = $request->query->get('availableSeats');
    $duration = $request->query->get('duration');
    $ecologique = $request->query->get('ecologique'); 


    // if ($departureDay) {
    //     $departureDay = \DateTime::createFromFormat('d/m/Y', $departureDay);
    //     if ($departureDay) {
    //         $departureDay->setTime(0, 0, 0); // Met l'heure � minuit pour ignorer l'heure
    //     }
    // }
    // if ($departureDay) {
    //     $departureDay = \DateTime::createFromFormat('d/m/Y', $departureDay);
    //     if ($departureDay) {
    //         $departureDay->setTime(0, 0, 0); 
    //     }
    // }
    // if ($availableSeats >= 1){
    //     $availableSeats = $availableSeats;
    //     } else {
    //     $availableSeats = 1;   
    // }
   
   
    $ridesQuery = $em->createQueryBuilder()
    ->select('r')
    ->from('App\Entity\Ride', 'r')
    ->join('r.vehicle', 'v')
    ->where('r.departure = :departure')
    ->andWhere('r.destination = :destination')
    //->andWhere('r.availableSeats > 0')
    //->andWhere('r.departureDay >= :departureDay')
    ->setParameter('departure', $departure)
    ->setParameter('destination', $destination);
   // ->setParameter('departureDay', $departureDay);
   
    
    // if ($departureDay) {  
    //     if ($departureDay instanceof \DateTime) {
    //         $formattedDate = $departureDay->format('Y-m-d');
    //         $ridesQuery->andWhere('r.departureDay >= :departureDay')
    //                 ->setParameter('departureDay', $formattedDate);
    //     }
    // }
    // if ($departureDay) {
    //     $ridesQuery->andWhere('r.departureDay >= :departureDay')
    //                ->setParameter('departureDay', $departureDay->format('Y-m-d'));
    // }
    // if ($departureDayFormatted) {
    //     $ridesQuery->andWhere('r.departureDay >= :departureDay')
    //                ->setParameter('departureDay', $departureDayFormatted);
    // }
    if (!empty($price)) {
        $ridesQuery->andWhere('r.price <= :price')
                   ->setParameter('price', $price);
    }
    if (!empty($availableSeats)) {
        $ridesQuery->andWhere('r.availableSeats >= :availableSeats')
                   ->setParameter('availableSeats', $availableSeats);
    }
    if (!empty($duration)) {
        $ridesQuery->andWhere('r.duration <= :duration')
                   ->setParameter('duration', $duration);
    }

   
    if (!empty($ecologique)) {  
        $ridesQuery->andWhere('v.energy = :energy')
                ->setParameter('energy', 'Electrique');
    }
    
    // if ($availableSeats) {
    //     $ridesQuery->andWhere('r.availableSeats <= :availableSeats')
    //                ->setParameter('availableSeats', $availableSeats);
    // }
    // if ($departureDay) {
    //     $ridesQuery->setParameter('departureDay', $departureDay);
    // }
  
    // var_dump($departureDayString, $departureDayFormatted);
    // echo $ridesQuery->getQuery()->getSQL();
    exit;
    
    $rides = $ridesQuery->getQuery()->getResult();
  
        return $this->render('ride/search_results.html.twig', [
            'form' => $form->createView(),
            'rides' => $rides,
        ]);
    }

   

}