<?php

namespace App\Controller\Admin;

use \App\Entity\Visitor;
use \App\Entity\Ride;
use \App\Entity\User;
use \App\Entity\registration;
use \App\Entity\Employe;
use App\Repository\RideRepository;
use App\Repository\BookingRepository;
use App\Repository\WinCreditRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class DashboardController extends AbstractDashboardController 
{
    private $rideRepository;
    private $bookingRepository;
    private $winCreditRepository;
   
    public function __construct(RideRepository $rideRepository, BookingRepository $bookingRepository,WinCreditRepository $winCreditRepository)
    {
        $this->rideRepository = $rideRepository;
        $this->bookingRepository = $bookingRepository;
        $this->winCreditRepository = $winCreditRepository;
    }
   
    // #[Route('/admin', name: 'admin')]
    // #[IsGranted('ROLE_ADMIN')]
    // public function index(): Response
    //     {
           
    //     $ridesData  = $this->rideRepository->countRidesByDay();
    //     return $this->render('admin/dashboard.html.twig', [
    //     'ridesdata' => $ridesData,
      
    // ]);
   
    // }

    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        $ridesByDay = $this->rideRepository->countRidesByDay();
        $bookingsByDay = $this->bookingRepository->countBookingsByDay();
        $totalCredits = $this->winCreditRepository->getTotalCredits();
        $creditsByDay = $this->winCreditRepository->getCreditsByDay();
        $ridesData  = $this->rideRepository->countRidesByDay();
             

        return $this->render('admin/dashboard.html.twig', [
            'ridesbyday' => $ridesByDay,
            'bookingsbyday' => $bookingsByDay,
            'totalCredits' => $totalCredits,
            'creditsByDay' => $creditsByDay,
            'ridesdata' => $ridesData,
            
        ]);
    }

    // #[Route('/admin/statistics', name: 'admin_statistics')]
    // #[IsGranted('ROLE_ADMIN')]
    // public function statistics(RideRepository $rideRepository, BookingRepository $bookingRepository): Response
    // {
    //     // Tu peux ici récupérer les données nécessaires pour les statistiques
    //     $ridesByDay = $rideRepository->countRidesByDay(); // Méthode personnalisée pour récupérer les trajets par jour
    //     $bookingsByDay = $bookingRepository->countBookingsByDay(); // Méthode personnalisée pour récupérer les réservations par jour

    //     // Passer les données au template des statistiques
    //     return $this->render('admin/statistics.html.twig', [
    //         'ridesbyday' => $ridesByDay,
    //         'bookingsbyday' => $bookingsByDay,
    //     ]);
    //     {
           
    //     $ridesData  = $this->rideRepository->countRidesByDay();
    //     return $this->render('admin/dashboard.html.twig', [
    //     'ridesdata' => $ridesData,
      
    // ]);
   
    // }

    // #[Route('/admin/statistics', name: 'admin_statistics')]
    // #[IsGranted('ROLE_ADMIN')]
    // public function statistics(RideRepository $rideRepository, BookingRepository $bookingRepository): Response
    // {
    //     // Tu peux ici récupérer les données nécessaires pour les statistiques
    //     $ridesByDay = $rideRepository->countRidesByDay(); // Méthode personnalisée pour récupérer les trajets par jour
    //     $bookingsByDay = $bookingRepository->countBookingsByDay(); // Méthode personnalisée pour récupérer les réservations par jour

    //     // Passer les données au template des statistiques
    //     return $this->render('admin/statistics.html.twig', [
    //         'ridesbyday' => $ridesByDay,
    //         'bookingsbyday' => $bookingsByDay,
    //     ]);
    // }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EcoRide');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateur', 'fa-solid fa-dollar-sign', User::class);
        yield MenuItem::linkToCrud('Employe', 'fas fa-clock', Employe::class);
        //yield MenuItem::linkToRoute('Statistiques', 'fa fa-chart-bar', 'admin_statistics');
        // yield MenuItem::linkToRoute('Statistiques', 'fa fa-chart-bar', 'admin_statistics');
        yield MenuItem::linkToLogout('DÃ©connexion', 'fa-solid fa-person-walking-arrow-right');
    }
    
    
    
    
}
