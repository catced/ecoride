<?php

namespace App\Controller;

use App\Repository\RideRepository;
use App\Repository\BookingRepository;
use App\Entity\Ride;
use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RideController extends AbstractController
{
    #[Route('/rides', name: 'ride_list')]
    public function list(RideRepository $rideRepository): Response
    {
        $rides = $rideRepository->findAll();
        return $this->render('ride/list.html.twig', [
            'rides' => $rides,
        ]);
    }

    #[Route('/ride/{id}', name: 'ride_details')]
    public function details($id, RideRepository $rideRepository): Response
    {
        $ride = $rideRepository->find($id);
        if (!$ride) {
            throw $this->createNotFoundException("Trajet non trouvé !");
        }

        return $this->render('ride/details.html.twig', [
            'ride' => $ride,
        ]);
    }

    // #[Route('/ride/{id}/book', name: 'book_rideconnexion')]
    // public function bookRideConnexion(Ride $ride, EntityManagerInterface $em): Response
    // {
    //     $user = $this->getUser();

    //     // 🔹 1. Vérifier si l'utilisateur est connecté
    //     if (!$user) {
    //         $this->addFlash('warning', 'Vous devez être connecté pour réserver un covoiturage.');
    //         return $this->redirectToRoute('app_login');
    //     }

    //     // 🔹 2. Vérifier le nombre de places disponibles
    //     if ($ride->getAvailableSeats() <= 0) {
    //         $this->addFlash('danger', 'Il n\'y a plus de places disponibles pour ce trajet.');
    //         return $this->redirectToRoute('ride_details', ['id' => $ride->getId()]);
    //     }

    //     // 🔹 3. Vérifier si l'utilisateur a assez de crédit
    //     if ($user->getCredit() < $ride->getPrice()) {
    //         $this->addFlash('danger', 'Vous n\'avez pas assez de crédit pour réserver ce trajet.');
    //         return $this->redirectToRoute('user_credit_recharge');
    //     }

    //     // 🔹 4. Réserver la place et débiter le crédit
    //     $user->setCredit($user->getCredit() - $ride->getPrice());
    //     $ride->setAvailableSeats($ride->getAvailableSeats() - 1);

    //     // 🔹 5. Sauvegarder en base de données
    //     $em->persist($user);
    //     $em->persist($ride);
    //     $em->flush();

    //     // 🔹 6. Afficher un message de confirmation
    //     $this->addFlash('success', 'Votre réservation a été confirmée !');

    //     return $this->redirectToRoute('user_dashboard'); 
    // }

#[Route('/reserve/{id}', name: 'ride_book', methods: ['POST'])]
public function bookRide(int $id, Ride $ride, Request $request, RideRepository $rideRepository, EntityManagerInterface $em) : Response
{
    $ride = $rideRepository->findWithVehicle($id);

    if (!$ride) {
        throw $this->createNotFoundException("Trajet non trouvé !");
    }

    // RÃ©cupÃ©ration du vÃ©hicule associÃ© au trajet
    $vehicle = $ride->getVehicle();
    if (!$vehicle) {
        throw $this->createNotFoundException("Aucun véhicule associé à ce trajet.");
    }

    // RÃ©cupÃ©ration du nombre de places demandÃ©es
    $seatsRequested = (int) $request->request->get('seats', 1);

    $user = $this->getUser();
    if (!$user) {
        $this->addFlash('error', "Vous devez être connecté pour réserver.");
        return $this->redirectToRoute('app_login');
    }


    // VÃ©rification du nombre de places disponibles
    if ($seatsRequested > 0 && $ride->getAvailableSeats() >= $seatsRequested) {
        // Mise Ã  jour des places disponibles
        $ride->setAvailableSeats($ride->getAvailableSeats() - $seatsRequested);

        $booking = new Booking();
        $booking->setRide($ride);
        $booking->setUser($user);
        $booking->setSeatsBooked($seatsRequested);

        dump($booking);
       
        $em->persist($booking);
        $em->persist($ride);
        $em->flush();
      
        $this->addFlash('success', "Vous avez réservé $seatsRequested place(s) pour le trajet du " . 
            $ride->getDepartureDay()->format('d/m/Y à  H:i') . " de " . 
            $ride->getDeparture() . " à  " . $ride->getDestination() . ".");
    } else {
        $this->addFlash('error', "Désolé, il n'y a pas assez de places disponibles.");
    }

    return $this->redirectToRoute('ride_details', ['id' => $ride->getId()]);
}

    #[Route('/my-rides', name: 'my_rides')]
    public function myRides(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour voir vos trajets.');
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les trajets proposés par le chauffeur connecté
        $rides = $em->getRepository(Ride::class)->findBy(['driver' => $user]);

        foreach ($rides as $ride) {
            if ($ride->getStatus() === null) {
                $ride->setStatus('pending'); // Définit un statut par défaut
                $em->persist($ride);
            }
        }
        $em->flush();
        
        return $this->render('User/my_rides.html.twig', [
            'rides' => $rides,
        ]);
    }  

    #[Route('/ride/cancel/{id}', name: 'ride_cancel', methods: ['POST'])]
    public function cancelRide(Ride $ride, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        // Vérifier si l'utilisateur connecté est bien le chauffeur
        if ($this->getUser() !== $ride->getDriver()) {
            $this->addFlash('error', 'Vous ne pouvez pas annuler ce trajet.');
            return $this->redirectToRoute('my_rides');
        }

        // Remboursement du chauffeur (ajout de 2 crédits)
        $driver = $ride->getDriver();
        $driver->setCredit($driver->getCredit() + 2);
        $entityManager->persist($driver);

        // Récupérer les réservations associées
        $bookings = $ride->getBookings();

        // Envoyer un email aux passagers inscrits
        foreach ($bookings as $booking) {
            $passenger = $booking->getUser();
            // Ici, tu peux appeler un service d'envoi d'email
            // Exemple : $emailService->sendCancellationEmail($passenger, $ride);
            $passenger = $booking->getUser();

            $email = (new Email())
                ->from('admin@covoiturage.com')
                ->to($passenger->getEmail())
                ->subject('Annulation de votre trajet')
                ->text("Bonjour " . $passenger->getPseudo() . ",\n\nLe trajet de " . $ride->getDeparture() . " à " . $ride->getDestination() . " a été annulé.\nMerci de vérifier d'autres trajets disponibles.");
            
            $mailer->send($email);

        }

        // Remboursement des crédits au chauffeur
        $user->setCredit($user->getCredit() + 2);
        $entityManager->persist($user);

        $ride->setStatus('cancelled');
        $entityManager->flush();
        // Supprimer le trajet et les réservations associées
        // $entityManager->remove($ride);
        // $entityManager->flush();

        $this->addFlash('success', 'Le trajet a été annulé, vous avez récupéré 2 crédits.');
        return $this->redirectToRoute('my_rides');
    }

    #[Route('/ride/start/{id}', name: 'ride_start', methods: ['POST'])]
    public function startRide(Ride $ride, EntityManagerInterface $em): Response
    {
        if ($ride->getStatus() === 'pending') {
            $ride->setStatus('ongoing');
            $em->flush();
            $this->addFlash('success', 'Le trajet a commencé !');
        }
        return $this->redirectToRoute('my-rides');
    }

   

    #[Route('/ride/complete/{id}', name: 'ride_complete', methods: ['POST'])]
    public function completeRide(Ride $ride, MailerInterface $mailer, EntityManagerInterface $em): Response
    {
        if ($ride->getStatus() === 'ongoing') {
           
            $ride->setStatus('completed');
           
            $em->flush();
        
            // Envoi d'un mail aux passagers
            // foreach ($ride->getBookings() as $booking) {
            //     $passenger = $booking->getUser();
            //     $email = (new Email())
            //         ->from('c.caty@c2jinfo.com')
            //         ->to($passenger->getEmail())
            //         ->subject('Validation de votre trajet')
            //         ->text('Merci de valider votre trajet sur notre plateforme.');

            //     $mailer->send($email);
            // }
            // $bookings = $ride->getBookings();
            // foreach ($bookings as $booking) {
            //     $passenger = $booking->getUser();
            //     // Ici, tu peux appeler un service d'envoi d'email
            //     // Exemple : $emailService->sendCancellationEmail($passenger, $ride);
            //     $passenger = $booking->getUser();
    
            //     $email = (new Email())
            //         ->from('admin@covoiturage.com')
            //         ->to($passenger->getEmail())
            //         ->subject('Annulation de votre trajet')
            //         ->text("Bonjour " . $passenger->getPseudo() . ",\n\nLe trajet de " . $ride->getDeparture() . " à " . $ride->getDestination() . " est termin�.");
                
            //     $mailer->send($email);
            //     dump($mailer);
            //     dump($email);
    
            // }

            // $this->addFlash('success', 'Trajet terminé. Mail envoyé aux passagers.');
        }
        return $this->redirectToRoute('my-rides');
    }

    #[Route('/ride/update-status/{id}', name: 'ride_update_status', methods: ['POST'])]
    public function updateRideStatus(Ride $ride, Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        $currentDate = new \DateTime(); // Récupère la date actuelle
        // $currentDate = $currentDate->format('d/m/Y');

        if ($ride->getDepartureDay() > $currentDate) {
         
        // if ($ride->getDepartureDay()->getTimestamp() > $currentDate->getTimestamp()) {
            $this->addFlash('error', 'Vous ne pouvez pas démarrer ce trajet avant sa date de départ.');
            return $this->redirectToRoute('my_rides');
        }
    
       
        if ($ride->getStatus() === 'pending') {
            $ride->setStatus('ongoing');
            $em->flush();
            $this->addFlash('success', 'Le trajet a commencé !');
        }
    
    
        // Vérifier que l'utilisateur est bien le chauffeur du trajet
        if ($ride->getDriver() !== $user) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier ce trajet.');
            return $this->redirectToRoute('my_rides');
        }
    
        // Récupérer le nouveau statut depuis le formulaire
        $newStatus = $request->request->get('status');
      
      
    
        // Vérifier si le statut est valide
        // if (!in_array($newStatus, ['ongoing', 'completed'])) {
        //     $this->addFlash('error', 'Statut invalide.');
        //     return $this->redirectToRoute('my_rides');
        // }

        if ($newStatus === null) {
            $this->addFlash('error', 'Le statut est manquant.');
            return $this->redirectToRoute('my_rides');
        }
        
        if (!in_array($newStatus, ['pending', 'ongoing', 'completed'])) {
            $this->addFlash('error', 'Statut invalide.');
            return $this->redirectToRoute('my_rides');
        }
        if ($newStatus === 'ongoing') {
            $ride->setStatus('ongoing');
            $em->persist($ride);
            $em->flush();
    
            $this->addFlash('success', 'Le trajet a commencé.');
        } else {
            $this->addFlash('danger', 'Statut incorrect.');
        }
        
      
        // Mettre à jour le statut du trajet
        $ride->setStatus($newStatus);
        $em->flush();
    
        $this->addFlash('success', 'Statut du trajet mis à jour.');
        if ($newStatus === 'completed'){  
            foreach ($ride->getBookings() as $booking) {
                $passenger = $booking->getUser();
    
                if (!$passenger || empty($passenger->getEmail())) {
                    continue; // S'assurer que l'utilisateur a bien un email
                }
    
                $email = (new Email())
                    ->from('no-reply@covoiturage.com')
                    ->to($passenger->getEmail())
                    ->subject('Votre trajet est termin�')
                    ->text("Bonjour " . $passenger->getPseudo() . ",\n\nLe trajet de " . $ride->getDeparture() . " � " . $ride->getDestination() . " est termin�. Merci de valider votre trajet sur notre plateforme.");
    
                try {
                    $mailer->send($email);
                } catch (\Exception $e) {
                    // Enregistrer l'erreur dans les logs pour le d�bogage
                    error_log("Erreur d'envoi d'email : " . $e->getMessage());
                }
            }
        }
        // $bookings = $ride->getBookings();
        // foreach ($bookings as $booking) {
        //     $passenger = $booking->getUser();
        //     // Ici, tu peux appeler un service d'envoi d'email
        //     // Exemple : $emailService->sendCancellationEmail($passenger, $ride);
        //     $passenger = $booking->getUser();

        //     $email = (new Email())
        //         ->from('admin@covoiturage.com')
        //         ->to($passenger->getEmail())
        //         ->subject('Annulation de votre trajet')
        //         ->text("Bonjour " . $passenger->getPseudo() . ",\n\nLe trajet de " . $ride->getDeparture() . " à " . $ride->getDestination() . " est termin�.");
            
        //     $mailer->send($email);
        //     dump($mailer);
        //     dump($email);

        // }

        // $this->addFlash('success', 'Trajet terminé. Mail envoyé aux passagers.');
        //  }   
        return $this->redirectToRoute('my_rides');
    }
    

}
