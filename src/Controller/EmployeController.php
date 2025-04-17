<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReviewRepository;
use App\Entity\Review;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;


class EmployeController extends AbstractController
{
    #[Route('/employe/dashboard', name: 'employe_dashboard')]
    public function dashboard(ReviewRepository $reviewRepository): Response
    {
        $reviewsToValidate = $reviewRepository->createQueryBuilder('r')
            ->where('r.rating >= 4')
            ->andWhere('r.comment IS NOT NULL')
            ->andWhere('r.validated = false')
            ->getQuery()
            ->getResult();

        $unsatisfiedRide = $reviewRepository->createQueryBuilder('r')
            ->join('r.ride', 'ride')
           // ->join('ride.passenger', 'passenger')
            ->join('ride.bookings', 'booking')
            ->join('booking.user', 'passenger')
            ->join('ride.driver', 'driver')
            ->where('r.rating <= 3')
            ->getQuery()
            ->getResult();



            return $this->render('employe/dashboard.html.twig', [
                'reviewsToValidate' => $reviewsToValidate,   // ? Ajout ici
                //'unsatisfiedTrips' => $unsatisfiedRide
                'unsatisfiedRide' => $unsatisfiedRide
            ]);
    }

    #[Route('/employe/reviews', name: 'employe_reviews')]
    public function listReviews(ReviewRepository $reviewRepository): Response
    {
        $reviews = $reviewRepository->findBy(['validated' => false]); // Récupère les avis non validés

        return $this->render('employe/reviews.html.twig', [
            'reviews' => $reviews
        ]);
    }

   
    #[Route('/employe/review/validate/{id}', name: 'validate_review')]
    public function validateReview(Review $review, EntityManagerInterface $entityManager): Response
    {
        $review->setvalidated(true); // Ajoute un champ isValidated dans ton entité Review
        $entityManager->flush();

        $this->addFlash('success', 'Avis validé avec succès.');
        return $this->redirectToRoute('employe_reviews');
    }

    #[Route('/employe/reviews-to-validate', name: 'employe_reviews_to_validate')]
    public function reviewsToValidate(ReviewRepository $reviewRepository): Response
    {
        $reviewsToValidate = $reviewRepository->createQueryBuilder('r')
            ->where('r.rating >= 4')
            ->andWhere('r.comment IS NOT NULL')
            ->andWhere('r.validated = false')
            ->getQuery()
            ->getResult();

        return $this->render('employe/reviews_to_validate.html.twig', [
            'reviewsToValidate' => $reviewsToValidate
        ]);
 
    }
    
    #[Route('/employe/unsatisfied_ride', name: 'employe_unsatisfied_ride')]
    public function unsatisfiedRide(ReviewRepository $reviewRepository): Response
    {
        $unsatisfiedRide = $reviewRepository->createQueryBuilder('r')
            ->join('r.ride', 'ride')
          //  ->join('r.passenger', 'passenger')

            ->join('ride.bookings', 'booking') // ? On passe par les réservations
            ->join('booking.user', 'passenger') 
            ->join('ride.driver', 'driver')
            ->where('r.rating <= 3')
            ->getQuery()
            ->getResult();

          
            
        return $this->render('employe/unsatisfied_ride.html.twig', [
            'unsatisfiedRide' => $unsatisfiedRide
        ]);
    }

    #[Route('/employe/send-mail-driver/{id}', name: 'send_mail_to_driver', methods: ['POST'])]
        public function sendMailToDriver(int $id, UserRepository $userRepository, \Symfony\Component\Mailer\MailerInterface $mailer): Response
        {
            $driver = $userRepository->find($id);

            if (!$driver) {
                throw $this->createNotFoundException('Chauffeur non trouvé.');
            }

            $email = (new \Symfony\Component\Mime\Email())
                ->from('avisnegatif@covoiturage.com')
                ->to($driver->getEmail())
                ->subject('Retour sur votre dernier voyage')
                ->text("Bonjour {$driver->getPseudo()},\n\nUn passager a signalé une insatisfaction concernant l'un de vos trajets. Merci de vérifier et de prendre contact si nécessaire.");

            $mailer->send($email);

            $this->addFlash('success', 'Email envoyé au chauffeur.');

            return $this->redirectToRoute('employe_unsatisfied_ride'); // Remplace par le nom de ta route
        }

    #[Route('/employe/review/delete/{id}', name: 'delete_review')]
    public function deleteReview(Review $review, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($review);
        $entityManager->flush();

        $this->addFlash('danger', 'Avis supprimé.');
        return $this->redirectToRoute('employe_reviews');
    }


}