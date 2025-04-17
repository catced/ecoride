<?php

namespace App\Controller\Admin;

use App\Repository\RideRepository;
use App\Repository\BookingRepository;
use App\Repository\WinCreditRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
    #[Route('/admin/statistics', name: 'admin_statistics')]
    public function index(RideRepository $rideRepository,BookingRepository $bookingRepository, WinCreditRepository $winCreditRepository): Response
    {
        // Récupérer les statistiques des trajets
        $ridesData = $rideRepository->countRidesByDay();
        $bookingsByDay = $bookingRepository->countBookingsByDay();

         // Récupérer les statistiques des crédits
         $winCredits = $winCreditRepository->findAll();
         $creditsByDay = $winCreditRepository->getCreditsByDay();
        
           // Convertir DateTime en string (format YYYY-MM-DD)
        foreach ($ridesData as &$ride) {
            $ride['day'] = $ride['day']->format('Y-m-d');
        }
        foreach ($bookingsByDay as &$booking) {
            $booking['day'] = $booking['day']->format('Y-m-d');
        }
        $creditsByDay = [
            'Monday' => 0,
            'Tuesday' => 0,
            'Wednesday' => 0,
            'Thursday' => 0,
            'Friday' => 0,
            'Saturday' => 0,
            'Sunday' => 0
        ];
        $totalCredits = 0;

        foreach ($winCredits as $credit) {
            $creditsByDay['Monday'] += $credit->getMonday() ?? 0;
            $creditsByDay['Tuesday'] += $credit->getTuesday() ?? 0;
            $creditsByDay['Wednesday'] += $credit->getWednesday() ?? 0;
            $creditsByDay['Thursday'] += $credit->getThursday() ?? 0;
            $creditsByDay['Friday'] += $credit->getFriday() ?? 0;
            $creditsByDay['Saturday'] += $credit->getSaturday() ?? 0;
            $creditsByDay['Sunday'] += $credit->getSunday() ?? 0;
        }
    
        // Calculer le total des crédits
        $totalCredits = array_sum($creditsByDay);

        

        return $this->render('admin/statistics.html.twig', [
             'ridesdata' => $ridesData, // Passage de la variable à Twig
             'bookingsbyday' => $bookingsByDay,
             'totalCredits' => $totalCredits,
             'creditsByDay' => $creditsByDay,
      
        //'ridesdata' => $rideRepository->countRidesByDay(), 
        //'bookingsbyday' => $bookingRepository->countBookingsByDay(),
        ]);
    }
}
