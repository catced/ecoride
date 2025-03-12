<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReviewRepository;
use App\Entity\Review;
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

        // $unsatisfiedRide = $reviewRepository->createQueryBuilder('r')
        //     ->join('r.ride', 'ride')
        //     ->join('ride.passenger', 'passenger')
        //     ->join('ride.driver', 'driver')
        //     ->where('r.rating <= 3')
        //     ->getQuery()
        //     ->getResult();



            return $this->render('employe/dashboard.html.twig', [
                'reviewsToValidate' => $reviewsToValidate,   // ? Ajout ici
              //  'unsatisfiedTrips' => $unsatisfiedRide
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


    #[Route('/employe/review/delete/{id}', name: 'delete_review')]
    public function deleteReview(Review $review, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($review);
        $entityManager->flush();

        $this->addFlash('danger', 'Avis supprimé.');
        return $this->redirectToRoute('employe_reviews');
    }


}