<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Employe;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Entity\WinCredit;
use App\Entity\Booking;
use App\Entity\Ride;

class EmployeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
      $user = new User();
      $user -> setId("1");
      $user -> setPseudo("catced");
      $user -> setName("catced");
      $user -> setrgpd("1");
      $user -> setissuspended("0");
      $user -> setCredit("20");
      $user -> setPassword("$2y$13.$qHbtaG02lEeBKZRTphLPZuDOUL0wr9oO4ukqLMnHnvtmA9VOJKYiy");
      $user -> setRoles(["ROLE_USER"]);
      $user -> setEmail("catced@gmail.com");
      $manager->persist($user);

      $employe = new Employe();
      $employe -> setPseudo("catced");
      $employe -> setPassword("$2y$13$10FmtRROnaNUT6ZeYoTA3OlSsfpcTEXBGmBmDJXBHd2ZDI937dq7e");
      $employe -> setRoles(["ROLE_ADMIN"]);
      $employe -> setEmail("catced@gmail.com");
      $manager->persist($employe);

      $employe = new Employe();
      $employe -> setPseudo("employe");
      $employe -> setPassword("$2y$13$10FmtRROnaNUT6ZeYoTA3OlSsfpcTEXBGmBmDJXBHd2ZDI937dq7e");
      $employe -> setRoles(["ROLE_EMPLOYE"]);
      $employe -> setEmail("employe@gmail.com");
      $manager->persist($employe);

      $user = new User();
      $user -> setId("2");
      $user -> setPseudo("Pierre");
      $user -> setPseudo("Pierre");
      $user -> setCredit("20");
      $user -> setPassword("$2y$13.$qHbtaG02lEeBKZRTphLPZuDOUL0wr9oO4ukqLMnHnvtmA9VOJKYiy");
      $user -> setRoles(["ROLE_USER"]);
      $user -> setEmail("pierre@gmail.com");
      $manager->persist($user);

      $user = new User();
      $user -> setId("3");
      $user -> setPseudo("Paul");
      $user -> setPseudo("Paul");
      $user -> setCredit("20");
      $user -> setPassword("$2y$13.$qHbtaG02lEeBKZRTphLPZuDOUL0wr9oO4ukqLMnHnvtmA9VOJKYiy");
      $user -> setRoles(["ROLE_USER"]);
      $user -> setEmail("paul@gmail.com");
      $manager->persist($user);

      $vehicle = new Vehicle();
      $vehicle -> setId("3");
      $vehicle -> setOwnerId("1");
      $vehicle -> setBrand("Renault");
      $vehicle -> setModel("Laguna");
      $vehicle -> setcolor("Bleue");
      $vehicle -> setlicensePlate("148DER55");
      $vehicle -> setSeatsCount("5");
      $vehicle -> setEnergy("Electrique");
      $vehicle -> setPreferences("non-fumeur");
      $vehicle -> setDateFirstUse(2021-06-01);
      $manager->persist($vehicle);

      $vehicle = new Vehicle();
      $vehicle -> setId("4");
      $vehicle -> setOwnerId("1");
      $vehicle -> setBrand("Peugeot");
      $vehicle -> setModel("308");
      $vehicle -> setcolor("Blanche");
      $vehicle -> setlicensePlate("874FGT55");
      $vehicle -> setSeatsCount("4");
      $vehicle -> setEnergy("Essence");
      $vehicle -> setPreferences("non-fumeur");
      $vehicle -> setDateFirstUse("2023-05-10");
      $manager->persist($vehicle);

      $vehicle = new Vehicle();
      $vehicle -> setId("5");
      $vehicle -> setOwnerId("2");
      $vehicle -> setBrand("Citroen");
      $vehicle -> setModel("C4");
      $vehicle -> setcolor("Noire");
      $vehicle -> setlicensePlate("654SED25");
      $vehicle -> setSeatsCount("4");
      $vehicle -> setEnergy("Essence");
      $vehicle -> setPreferences("fumeur");
      $vehicle -> setDateFirstUse("2023-09-21");
      $manager->persist($vehicle);

      $wincredit = new WinCredit();
      $wincredit -> setMonday("4");
      $wincredit -> setTuesday("6");
      $wincredit -> setWednesday("12");
      $wincredit -> setThursday("6");
      $wincredit -> setFriday("8");
      $wincredit -> setSaturday("20");
      $wincredit -> setMonday("6");
      $manager->persist($wincredit);

      $ride = new Ride();
      $ride -> setVehicleId("4");
      $ride -> setDriverId("1");
      $ride -> setDeparture("Paris");
      $ride -> setDestination("Lyon");
      $ride -> setDepartureDay("2025-09-10");
      $ride -> setDepartureTime("08:30");
      $ride -> setPrice("25");
      $ride -> setDuration("04:00");
      $ride -> setStatus("pending");
      $ride -> setAvailableSeats("4");

      $manager->persist($ride);

      $ride = new Ride();
      $ride -> setVehicleId("4");
      $ride -> setDriverId("1");
      $ride -> setDeparture("Lyon");
      $ride -> setDestination("Nice");
      $ride -> setDepartureDay1("2025-09-12");
      $ride -> setDepartureTime("14:30");
      $ride -> setPrice("55");
      $ride -> setDuration("06:00");
      $ride -> setStatus("pending");
      $ride -> setAvailableSeats("4");

      $manager->persist($ride);

      $ride = new Ride();
      $ride -> setVehicleId("3");
      $ride -> setDriverId("1");
      $ride -> setDeparture("Nantes");
      $ride -> setDestination("Paris");
      $ride -> setDepartureDay("2025-09-01");
      $ride -> setDepartureTime("12:25");
      $ride -> setPrice("25");
      $ride -> setDuration("04:15");
      $ride -> setStatus("pending");
      $ride -> setAvailableSeats("4");

      $manager->persist($ride);

      $ride = new Ride();
      $ride -> setVehicleId("5");
      $ride -> setDriverId("2");
      $ride -> setDeparture("Nantes");
      $ride -> setDestination("Paris");
      $ride -> setDepartureDay("2025-02-01");
      $ride -> setDepartureTime("12:25");
      $ride -> setPrice("25");
      $ride -> setDuration("04:15");
      $ride -> setStatus("pending");
      $ride -> setAvailableSeats("4");

      $manager->persist($ride);

      $manager->flush();
    }

}