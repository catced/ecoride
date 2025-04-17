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



class Search_ResultsController extends AbstractController
{
    #[Route('/search_results', name: 'search_results', methods: ['GET'])]
public function search(Request $request, RideRepository $rideRepository, EntityManagerInterface $em): Response
{
    $departure = $request->query->get('departure');
    $destination = $request->query->get('destination');
    $departureDayString = $request->query->get('departureDay');
    $price = $request->query->get('price');
    $availableSeats = $request->query->get('availableSeats');
    $duration = $request->query->get('duration');
    $ecologique = $request->query->get('ecologique');
    
    $qb = $em->createQueryBuilder()
        ->select('r')
        ->from(Ride::class, 'r')
        ->join('r.vehicle', 'v')
        ->where('1=1'); // Permet d'ajouter dynamiquement les conditions

    if ($departure) {
        $qb->andWhere('r.departure LIKE :departure')
            ->setParameter('departure', '%' . $departure . '%');
    }

    if ($destination) {
        $qb->andWhere('r.destination LIKE :destination')
            ->setParameter('destination', '%' . $destination . '%');
    }

    if ($departureDayString) {
        $departureDay = \DateTime::createFromFormat('d/m/Y', $departureDayString);
        if ($departureDay) {
            $qb->andWhere('r.departureDay >= :departureDay')
                ->setParameter('departureDay', $departureDay->format('Y-m-d'));
        }
    } else {
        // Si aucune date n'est spécifiée, exclure les voyages passés
        $today = new \DateTime();
        $qb->andWhere('r.departureDay >= :today')
            ->setParameter('today', $today->format('Y-m-d'));
    }

    if ($price) {
        $qb->andWhere('r.price <= :price')
            ->setParameter('price', $price);
    }

    if ($availableSeats) {
        $qb->andWhere('r.availableSeats >= :availableSeats')
            ->setParameter('availableSeats', $availableSeats);
    }

    if ($duration) {
        $qb->andWhere('r.duration <= :duration')
            ->setParameter('duration', $duration);
    }

    if (!empty($ecologique)) {
        $qb->andWhere('v.energy = :energy')
            ->setParameter('energy', 'Electrique');
    }

    // Obtenir les résultats
    $rides = $qb->getQuery()->getResult();

    return $this->render('ride/search_results.html.twig', [
        'rides' => $rides,
    ]);
}
}
