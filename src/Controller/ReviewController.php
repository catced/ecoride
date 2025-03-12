<?php
namespace App\Controller;

use App\Entity\Review;
use App\Entity\Booking;
use App\Entity\Ride;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use App\Repository\RideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class ReviewController extends AbstractController
{
    #[Route('/ride/review/{id}', name: 'ride_review', methods: ['POST'])]
    public function reviewRide(int $id, Request $request, EntityManagerInterface $em, MailerInterface $mailer, RideRepository $rideRepository): Response
    {
        $user = $this->getUser();

        $ride = $rideRepository->find($id);
        
        // Vérifier si le trajet est bien terminé
        if ($ride->getStatus() !== 'completed') {
          
            $this->addFlash('error', 'Vous ne pouvez pas noter un trajet qui n?est pas terminé.');
            return $this->redirectToRoute('my_rides');
        }
       
        $booking = $em->getRepository(Booking::class)->findOneBy([
            'ride' => $ride,
            'user' => $user
        ]);
       
        // Vérifier si l'utilisateur a bien participé au trajet
         if (!$ride->getBookings()->exists(fn($key, $booking) => $booking->getUser() === $user)) {
            //if ($booking){
            $this->addFlash('error', 'Vous ne pouvez pas noter un trajet auquel vous n?avez pas participé.');
            return $this->redirectToRoute('my_rides');
        }

        // Vérifier si l'utilisateur a déjà laissé un avis
        $existingReview = $em->getRepository(Review::class)->findOneBy([
            'ride' => $ride,
            'passenger' => $user
        ]);

        if ($existingReview) {
            $this->addFlash('error', 'Vous avez déjà noté ce trajet.');
            return $this->redirectToRoute('my_rides');
        }

        // Récupérer les données du formulaire
        $rating = (int) $request->request->get('rating');
        $comment = $request->request->get('comment');

        // Vérifier la note
        if ($rating < 1 || $rating > 5) {
            $this->addFlash('error', 'La note doit être entre 1 et 5.');
            return $this->redirectToRoute('my_rides');
        }

        // Création et sauvegarde de l'avis
        $review = new Review();
        $review->setRide($ride);
        $review->setPassenger($user);
        $review->setRating($rating);
        $review->setComment($comment);

        // Créditer le chauffeur immédiatement si la note est >= 4
        if ($rating >= 4) {
            $ride->getDriver()->setCredit($ride->getDriver()->getCredit() + 2);
        } else {
            $review->setValidated(false); // Un admin doit valider l'avis négatif
        }

        $em->persist($review);
        $em->flush();
        $passenger = $booking->getUser();
        
        foreach ($ride->getBookings() as $booking) {
            $email = (new Email())
            ->from('avisnegatif@covoiturage.com')
        // ->to('support@covoiturage.com')
            ->to($passenger->getEmail())
            ->subject('Problème signalé')
            ->text("Un passager a signalé un problème sur le trajet {$booking->getRide()->getId()}: $comment");

            $mailer->send($email);
        }
        $this->addFlash('success', 'Votre avis a été enregistré.');
        //return $this->redirectToRoute('my_rides');

        return $this->render('user/voyages.html.twig', [
            'ride' => $ride,
            'review' => $review, // On envoie l'avis même s'il n'est pas validé
        ]);
    }

    #[Route('/user/reviews', name: 'user_reviews')]
    public function manageReviews(ReviewRepository $reviewRepo, EntityManagerInterface $em): Response
    {
        $reviews = $reviewRepo->findBy(['validated' => false]);

        return $this->render('user/reviews.html.twig', ['reviews' => $reviews]);
    }

    // #[Route('/employe/rides-reviews', name: 'employe_rides_reviews')]
    // public function ridesReviews(RideRepository $rideRepository): Response
    // {
    //     $rides = $rideRepository->getRidesByReview();

    //     return $this->render('employe/rides_reviews.html.twig', [
    //         'rides' => $rides,
    //     ]);
    // }


    #[Route('/user/review/validate/{id}', name: 'user_review_validate')]
    public function validateReview(Review $review, EntityManagerInterface $em): Response
    {
        $review->setValidated(true);
        $review->getRide()->getDriver()->setCredit($review->getRide()->getDriver()->getCredit() + 2);
        $em->flush();

        $this->addFlash('success', 'Avis validé et chauffeur crédité.');
        return $this->redirectToRoute('user_reviews');
    }
}
