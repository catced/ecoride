<?php

namespace App\Controller\Admin;

use \App\Entity\Visitor;
use \App\Entity\Ride;
use \App\Entity\User;
use \App\Entity\registration;
use \App\Entity\Employe;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController 
{
   #[Route('/admin', name: 'admin')]
   #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        
        return $this->render('admin/dashboard.html.twig');
               
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EcoRide');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('Type de bateau', 'fas fa-person', Ride::class);
       // yield MenuItem::linkToCrud('Réservation', 'fas fa-car', registration::class);
        yield MenuItem::linkToCrud('Utilisateur', 'fa-solid fa-dollar-sign', User::class);
       // yield MenuItem::linkToCrud('Visiteur', 'fas fa-clock', Visitor::class);
        yield MenuItem::linkToCrud('Employe', 'fas fa-clock', Employe::class);
     
        yield MenuItem::linkToLogout('DÃ©connexion', 'fa-solid fa-person-walking-arrow-right');
    }
}
