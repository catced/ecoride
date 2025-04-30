<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Employe;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Entity\WinCredit;
use App\Entity\Ride;
use App\Entity\Booking;
use App\Entity\Review;

class EmployeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Employés
        $employe = new Employe();
        $employe->setPseudo("jose");
        $employe->setPassword("$2y$13$10FmtRROnaNUT6ZeYoTA3OlSsfpcTEXBGmBmDJXBHd2ZDI937dq7e");
        $employe->setRoles(["ROLE_ADMIN"]);
        $employe->setEmail("jose@gmail.com");
        $manager->persist($employe);

        $employe = new Employe();
        $employe->setPseudo("employe");
        $employe->setPassword("$2y$13$10FmtRROnaNUT6ZeYoTA3OlSsfpcTEXBGmBmDJXBHd2ZDI937dq7e");
        $employe->setRoles(["ROLE_EMPLOYE"]);
        $employe->setEmail("employe@gmail.com");
        $manager->persist($employe);

        // Utilisateurs
        $user = new User();
        $user->setPseudo("roro");
        $user->setName("roro");
        $user->setRgpd(true);
        $user->setIsSuspended(false);
        $user->setCredit(20);
        $user->setPassword('C@tced01');
        $user->setRoles(["ROLE_USER"]);
        $user->setEmail("roro@gmail.com");
        $manager->persist($user);
        $this->addReference('roro', $user);

        $user = new User();
        $user->setPseudo("Pierre");
        $user->setName("Pierre");
        $user->setRgpd(true);
        $user->setIsSuspended(false);
        $user->setCredit(20);
        $user->setPassword('$2y$13$a8J9cUOm/bby6kRaMLbZ7eJEmXpV8mWKpDRFuZgyOtr2YjI8Zi6RW');
        $user->setRoles(["ROLE_USER"]);
        $user->setEmail("pierre@gmail.com");
        $manager->persist($user);
        $this->addReference('Pierre', $user);

        $user = new User();
        $user->setPseudo("Paul");
        $user->setName("Paul");
        $user->setRgpd(true);
        $user->setIsSuspended(false);
        $user->setCredit(20);
        $user->setPassword('$2y$13$a8J9cUOm/bby6kRaMLbZ7eJEmXpV8mWKpDRFuZgyOtr2YjI8Zi6RW');
        $user->setRoles(["ROLE_USER"]);
        $user->setEmail("paul@gmail.com");
        $manager->persist($user);
        $this->addReference('Paul', $user);

        // Véhicules
        $vehicle = new Vehicle();
        $vehicle->setOwner($this->getReference('Paul', User::class));
        $vehicle->setBrand("Renault");
        $vehicle->setModel("Laguna");
        $vehicle->setColor("Bleue");
        $vehicle->setLicensePlate("148DER55");
        $vehicle->setSeatsCount(5);
        $vehicle->setEnergy("Electrique");
        $vehicle->setPreferences(["non-fumeur"]);
        $vehicle->setDateFirstUse(new \DateTime('2021-01-01'));
        $manager->persist($vehicle);
        $this->addReference('Vehicle_Paul', $vehicle);

        $vehicle = new Vehicle();
        $vehicle->setOwner($this->getReference('Pierre', User::class));
        $vehicle->setBrand("Peugeot");
        $vehicle->setModel("308");
        $vehicle->setColor("Blanche");
        $vehicle->setLicensePlate("874FGT55");
        $vehicle->setSeatsCount(4);
        $vehicle->setEnergy("Essence");
        $vehicle->setPreferences(["non-fumeur"]);
        $vehicle->setDateFirstUse(new \DateTime('2023-06-01'));
        $manager->persist($vehicle);
        $this->addReference('Vehicle_Pierre', $vehicle);

        $vehicle = new Vehicle();
        $vehicle->setOwner($this->getReference('Paul', User::class));
        $vehicle->setBrand("Citroen");
        $vehicle->setModel("C4");
        $vehicle->setColor("Noire");
        $vehicle->setLicensePlate("654SED25");
        $vehicle->setSeatsCount(4);
        $vehicle->setEnergy("Essence");
        $vehicle->setPreferences(["fumeur"]);
        $vehicle->setDateFirstUse(new \DateTime('2023-09-03'));
        $manager->persist($vehicle);
        $this->addReference('Vehicle_Paul1', $vehicle);

        $vehicle = new Vehicle();
        $vehicle->setOwner($this->getReference('Pierre', User::class));
        $vehicle->setBrand("Toyota");
        $vehicle->setModel("Yaris");
        $vehicle->setColor("Rouge");
        $vehicle->setLicensePlate("321POI77");
        $vehicle->setSeatsCount(4);
        $vehicle->setEnergy("Hybride");
        $vehicle->setPreferences(["non-fumeur"]);
        $vehicle->setDateFirstUse(new \DateTime('2022-02-15'));
        $manager->persist($vehicle);
        $this->addReference('Vehicle_Pierre1', $vehicle);

        // WinCredit
        $wincredit = new WinCredit();
        $wincredit->setMonday("6");
        $wincredit->setTuesday("6");
        $wincredit->setWednesday("12");
        $wincredit->setThursday("6");
        $wincredit->setFriday("8");
        $wincredit->setSaturday("20");
        $manager->persist($wincredit);

        // Rides
        $ride = new Ride();
        $ride->setVehicle($this->getReference('Vehicle_Paul', Vehicle::class));
        $ride->setDriver($this->getReference('Paul', User::class));
        $ride->setDeparture("Paris");
        $ride->setDestination("Lyon");
        $ride->setDepartureDay(new \DateTime('2025-09-03'));
        $ride->setDepartureTime(new \DateTime('2025-09-03 08:30'));
        $ride->setPrice("25");
        $ride->setDuration("04:00");
        $ride->setStatus("pending");
        $ride->setAvailableSeats("4");
        $manager->persist($ride);
        $this->addReference('Ride1', $ride);

        $ride = new Ride();
        $ride->setVehicle($this->getReference('Vehicle_Paul1', Vehicle::class));
        $ride->setDriver($this->getReference('Paul', User::class));
        $ride->setDeparture("Lyon");
        $ride->setDestination("Nice");
        $ride->setDepartureDay(new \DateTime('2025-06-15'));
        $ride->setDepartureTime(new \DateTime('2025-06-15 14:30'));
        $ride->setPrice("55");
        $ride->setDuration("06:00");
        $ride->setStatus("pending");
        $ride->setAvailableSeats("4");
        $manager->persist($ride);
        $this->addReference('Ride2', $ride);

        $ride = new Ride();
        $ride->setVehicle($this->getReference('Vehicle_Pierre', Vehicle::class));
        $ride->setDriver($this->getReference('Pierre', User::class));
        $ride->setDeparture("Nantes");
        $ride->setDestination("Paris");
        $ride->setDepartureDay(new \DateTime('2025-06-15'));
        $ride->setDepartureTime(new \DateTime('2025-06-15 12:25'));
        $ride->setPrice("25");
        $ride->setDuration("04:15");
        $ride->setStatus("pending");
        $ride->setAvailableSeats("4");
        $manager->persist($ride);
        $this->addReference('Ride3', $ride);

        $ride = new Ride();
        $ride->setVehicle($this->getReference('Vehicle_Pierre1', Vehicle::class));
        $ride->setDriver($this->getReference('Pierre', User::class));
        $ride->setDeparture("Nantes");
        $ride->setDestination("Paris");
        $ride->setDepartureDay(new \DateTime('2025-09-15'));
        $ride->setDepartureTime(new \DateTime('2025-09-15 13:00'));
        $ride->setPrice("25");
        $ride->setDuration("04:15");
        $ride->setStatus("pending");
        $ride->setAvailableSeats("4");
        $manager->persist($ride);
        $this->addReference('Ride4', $ride);

        $manager->flush();

        $booking = new Booking();
        $booking->setCreatedAt(new \DateTime('2025-06-15'));
        $booking->setSeatsBooked(1);
        $booking->setUser($this->getReference('Pierre', User::class));
        $booking->setRide($this->getReference('Ride1', Ride::class));
        $manager->persist($booking);

        $booking = new Booking();
        $booking->setCreatedAt(new \DateTime('2025-06-15'));
        $booking->setSeatsBooked(1);
        $booking->setUser($this->getReference('roro', User::class));
        $booking->setRide($this->getReference('Ride1', Ride::class));
        $manager->persist($booking);

        $booking = new Booking();
        $booking->setCreatedAt(new \DateTime('2025-05-10'));
        $booking->setSeatsBooked(1);
        $booking->setUser($this->getReference('roro', User::class));
        $booking->setRide($this->getReference('Ride2', Ride::class));
        $manager->persist($booking);

        $booking = new Booking();
        $booking->setCreatedAt(new \DateTime('2025-04-10'));
        $booking->setSeatsBooked(1);
        $booking->setUser($this->getReference('Paul', User::class));
        $booking->setRide($this->getReference('Ride3', Ride::class));
        $manager->persist($booking);

        $booking = new Booking();
        $booking->setCreatedAt(new \DateTime('2025-03-01'));
        $booking->setSeatsBooked(1);
        $booking->setUser($this->getReference('Paul', User::class));
        $booking->setRide($this->getReference('Ride3', Ride::class));
        $manager->persist($booking);

        $manager->flush();

        $review = new Review();
        $review->setComment("Chauffeur au top. Eau offerte");
        $review->setRating(4);
        $review->setValidated(false);
        $review->setPassenger($this->getReference('roro', User::class));
        $review->setRide($this->getReference('Ride2', Ride::class));
        $manager->persist($review);
        $manager->flush();

        $review = new Review();
        $review->setComment("Pas de musique. La voiture est sale et les sièges déchirés !!!!");
        $review->setRating(1);
        $review->setValidated(false);
        $review->setPassenger($this->getReference('roro', User::class));
        $review->setRide($this->getReference('Ride1', Ride::class));
        $manager->persist($review);
        $manager->flush();

        $review = new Review();
        $review->setComment("");
        $review->setRating(4);
        $review->setValidated(false);
        $review->setPassenger($this->getReference('Pierre', User::class));
        $review->setRide($this->getReference('Ride3', Ride::class));
        $manager->persist($review);
        $manager->flush();

       

    }
}
