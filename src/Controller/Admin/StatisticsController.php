<?php

namespace App\Controller\Admin;

use App\Repository\RideRepository;
use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
    #[Route('/admin/statistics', name: 'admin_statistics')]
    public function index(RideRepository $rideRepository,BookingRepository $bookingRepository): Response
    {
        // Récupérer les statistiques des trajets
        $ridesData = $rideRepository->countRidesByDay();
        $bookingsByDay = $bookingRepository->countBookingsByDay();

           // Convertir DateTime en string (format YYYY-MM-DD)
        foreach ($ridesData as &$ride) {
            $ride['day'] = $ride['day']->format('Y-m-d');
        }
        foreach ($bookingsByDay as &$booking) {
            $booking['day'] = $booking['day']->format('Y-m-d');
        }
        // dd($ridesData);
        return $this->render('admin/statistics.html.twig', [
             'ridesdata' => $ridesData, // Passage de la variable à Twig
             'bookingsbyday' => $bookingsByDay,
        //'ridesdata' => $rideRepository->countRidesByDay(), 
        //'bookingsbyday' => $bookingRepository->countBookingsByDay(),
        ]);
    }
}
