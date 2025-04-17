<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Entity\Ride;
use App\Entity\User;
use App\Entity\WinCredit;
use App\Form\VehicleFormType;
use App\Form\RideFormType;
use App\Repository\RideRepository;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_userdashboard')]
    public function dashboard(Request $request, EntityManagerInterface $em, RideRepository $rideRepository, BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser();
        $role = $user->getRole();

        $vehicles = $em->getRepository(Vehicle::class)->findBy(['owner' => $user]);

        $vehicle = new Vehicle();
        $vehicle->setOwner($user);
        $vehicleForm = $this->createForm(VehicleFormType::class, $vehicle);
        $vehicleForm->handleRequest($request);

        if ($vehicleForm->isSubmitted() && $vehicleForm->isValid()) {
            $em->persist($vehicle);
            $em->flush();
            $this->addFlash('success', 'Véhicule enregistré avec succès !');
            return $this->redirectToRoute('app_userdashboard');
        }

        $ride = new Ride();
        $ride->setDriver($user);
        $rideForm = $this->createForm(RideFormType::class, $ride, [
            'vehicles' => $vehicles,
        ]);
        $rideForm->handleRequest($request);

        if ($rideForm->isSubmitted() && $rideForm->isValid()) {
            if ($user->getCredit() < 2) {
                $this->addFlash('error', 'Vous n\'avez pas assez de crédits pour proposer un voyage.');
            } else {
                $user->setCredit($user->getCredit() - 2);

                $departureDay = $ride->getDepartureDay();
                $dayOfWeek = $departureDay->format('l');

                $winCreditRepo = $em->getRepository(WinCredit::class);
                $winCredit = $winCreditRepo->findOneBy([]); // À adapter selon ta logique

                if (!$winCredit) {
                    $winCredit = new WinCredit();
                }

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

                $em->persist($user);
                $em->persist($ride);
                $em->persist($winCredit);
                $em->flush();

                $this->addFlash('success', 'Trajet proposé avec succès !');
                return $this->redirectToRoute('app_userdashboard');
            }
        }

        $ridesAsDriver = $rideRepository->findUpcomingRidesForDriver($user, 10) ?? [];

        $ridesAsPassenger = [];
        if ($role === 'passager') {
            $ridesAsPassenger = $bookingRepository->findUpcomingRidesForPassenger($user, 10) ?? [];
        }

        return $this->render('user/userdashboard.html.twig', [
            'vehicleForm' => $vehicleForm->createView(),
            'rideForm' => $rideForm->createView(),
            'vehicles' => $vehicles,
            'ridesAsDriver' => $ridesAsDriver,
            'ridesAsPassenger' => $ridesAsPassenger,
            'role' => $role,
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
            $departureDay = new \DateTime();
        }

        $price = $request->query->get('price');
        $availableSeats = $request->query->get('availableSeats');
        $duration = $request->query->get('duration');

        $qb = $em->getRepository(Ride::class)->createQueryBuilder('r');

        if ($departure) {
            $qb->andWhere('r.departure LIKE :departure')
                ->setParameter('departure', '%' . $departure . '%');
        }

        if ($destination) {
            $qb->andWhere('r.destination LIKE :destination')
                ->setParameter('destination', '%' . $destination . '%');
        }

        if (!empty($price)) {
            $qb->andWhere('r.price <= :price')
                ->setParameter('price', $price);
        }

        if (!empty($availableSeats)) {
            $qb->andWhere('r.availableSeats >= :availableSeats')
                ->setParameter('availableSeats', $availableSeats);
        }

        if (!empty($duration)) {
            $qb->andWhere('r.duration <= :duration')
                ->setParameter('duration', $duration);
        }

        if (!empty($ecologique)) {  
            $qb->andWhere('v.energy = :energy')
                       ->setParameter('energy', 'Electrique');
        }
        $rides = $qb->getQuery()->getResult();

        return $this->render('ride/search_results.html.twig', [
            'rides' => $rides,
        ]);
    }
}


