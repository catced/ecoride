<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Entity\Ride;
use App\Entity\User;
use App\Entity\WinCredit;
use App\Form\VehicleFormType;
use App\Form\RideFormType;
use App\Repository\RideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserDashboardController1 extends AbstractController
{
    #[Route('/userdashboard', name: 'app_userdashboard')]
    public function dashboard(Request $request, EntityManagerInterface $em, RideRepository $rideRepository): Response
    {
        $user = $this->getUser();
        //dd($this->getUser(), $this->getUser() instanceof User);

        $vehicles = $em->getRepository(Vehicle::class)->findBy(['owner' => $user]);
         /*** FORMULAIRE D'AJOUT DE VÉHICULE ***/
         $vehicle = new Vehicle();
         $vehicle->setOwner($user);
    //      dump($user); 
    //      if (!$someObject instanceof User) {
    //         throw new \InvalidArgumentException('Le propriétaire du véhicule doit être un utilisateur.');
    //     }
        
         
    //    $vehicle->setOwner($someObject);
         $vehicleForm = $this->createForm(VehicleFormType::class, $vehicle);
         $vehicleForm->handleRequest($request);
 
         if ($vehicleForm->isSubmitted() && $vehicleForm->isValid()) {
             $em->persist($vehicle);
             $em->flush();
             $this->addFlash('success', 'Véhicule enregistré avec succès !');
             return $this->redirectToRoute('app_userdashboard');
         }
 
         /*** FORMULAIRE D'AJOUT DE TRAJET ***/
         $ride = new Ride();
         $ride->setDriver($user);
         $rideForm = $this->createForm(RideFormType::class, $ride, [
             'vehicles' => $vehicles, // Passe les véhicules à RideFormType
         ]);
         $rideForm->handleRequest($request);

        // Assigner le nombre de places disponibles
        
       
 
         if ($rideForm->isSubmitted() && $rideForm->isValid()) {
            $selectedVehicle = $ride->getVehicle();
            if ($selectedVehicle) {
                $ride->setAvailableSeats($selectedVehicle->getSeatsCount());
            } else {
                // Si aucun véhicule n'est sélectionné, définir une valeur par défaut
                $ride->setAvailableSeats(0);
            }
            
             if ($user->getCredit() < 2) {
                 $this->addFlash('error', 'Vous n\'avez pas assez de crédits pour proposer un voyage.');
             } else {
                 // Déduire 2 crédits et enregistrer le trajet
                 $user->setCredit($user->getCredit() - 2);

                $departureDay = $ride->getDepartureDay(); // Récupère la date de départ choisie
                $dayOfWeek = $departureDay->format('l'); // Extrait le jour en anglais (Monday, Tuesday, etc.)

                // Trouver ou créer une entrée WinCredit
                $winCreditRepo = $em->getRepository(WinCredit::class);
                $winCredit = $winCreditRepo->findOneBy([]); // Récupère l'unique enregistrement existant (à adapter selon votre logique)

                if (!$winCredit) {
                    $winCredit = new WinCredit();
                }

                // Associe les 2 crédits prélevés au bon jour de la semaine
                switch ($dayOfWeek) {
                    case 'Monday':
                        $winCredit->setMonday(($winCredit->getMonday() ?? 0) + 2);
                        break;
                    case 'Tuesday':
                        $winCredit->setTuesday(($winCredit->getTuesday() ?? 0) + 2);
                        break;
                    case 'Wednesday':
                        $winCredit->setWednesday(($winCredit->getWednesday() ?? 0) + 2);
                        break;
                    case 'Thursday':
                        $winCredit->setThursday(($winCredit->getThursday() ?? 0) + 2);
                        break;
                    case 'Friday':
                        $winCredit->setFriday(($winCredit->getFriday() ?? 0) + 2);
                        break;
                    case 'Saturday':
                        $winCredit->setSaturday(($winCredit->getSaturday() ?? 0) + 2);
                        break;
                    case 'Sunday':
                        $winCredit->setSunday(($winCredit->getSunday() ?? 0) + 2);
                        break;
                }

                // Sauvegarde les changements
                $em->persist($winCredit);


                 $em->persist($ride);
                 $em->flush();
                 $this->addFlash('success', 'Trajet proposé avec succès !');
                 return $this->redirectToRoute('app_userdashboard');
             }
         }

          // Récupère les trajets où l'utilisateur est conducteur
        $ridesAsDriver = $rideRepository->findUpcomingRidesForDriver($user, 10) ?? [];
        
        
         return $this->render('user/userdashboard.html.twig', [
             'vehicleForm' => $vehicleForm->createView(),
             'rideForm' => $rideForm->createView(),
             'vehicles' => $vehicles,
             'ridesAsDriver' => $ridesAsDriver,
         ]);
     }

     #[Route('/search_results', name: 'search_results')]
        public function searchResults(Request $request, EntityManagerInterface $em): Response
        {
            $departure = $request->query->get('departure');
            $destination = $request->query->get('destination');
            $departureDayString = $request->query->get('departureDay');
            if ($departureDayString) {
                $departureDay = \DateTime::createFromFormat('d/m/Y', $departureDayString);
                
                if (!$departureDay) {
                    throw new \Exception("Format de date invalide !");
                }
            } else {
                $departureDay = new \DateTime(); // Prendre la date du jour si aucune date fournie
            }
            $price = $request->query->get('price');
            $availableSeats = $request->query->get('availableSeats');
            $duration = $request->query->get('duration');

            $qb = $em->getRepository(Ride::class)->createQueryBuilder('r');
          
            if ($departure) {
                $qb->andWhere('r.departure LIKE :departure')
                ->setParameter('departure', '%'.$departure.'%');
            }
            if ($destination) {
                $qb->andWhere('r.destination LIKE :destination')
                ->setParameter('destination', '%'.$destination.'%');
            }
            if ($departureDay) {
                $formattedDate = $departureDay->format('Y-m-d');
                $qb->andWhere('r.departureDay >= :departureDay')
                ->setParameter('departureDay', $formattedDate);
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
            
            $rides = $qb->getQuery()->getResult();

            return $this->render('ride/search_results.html.twig', [
                'rides' => $rides,
            ]);
        }

}
