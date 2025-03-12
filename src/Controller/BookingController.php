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
use Symfony\Component\Security\Core\User\UserInterface;
use App\Controller\SessionInterface;


class BookingController extends AbstractController
{
    #[Route('/booking/validate/{id}', name: 'validate_ride', methods: ['POST'])]
    public function validateRide(Booking $booking, EntityManagerInterface $em): Response
    {
        $driver = $booking->getRide()->getDriver();
        $driver->setCredits($driver->getCredits() + 5); // Ex: +5 cr�dits par passager valid�

        $em->flush();
        $this->addFlash('success', 'Trajet valid�, le chauffeur a re�u ses cr�dits.');
        return $this->redirectToRoute('my_bookings');
    }

    #[Route('/booking/report/{id}', name: 'report_issue', methods: ['POST'])]
    public function reportIssue(Request $request, Booking $booking, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $comment = $request->request->get('comment');

        // Envoi d?un email � l?�quipe support
        $email = (new Email())
            ->from('noreply@covoiturage.com')
           // ->to('support@covoiturage.com')
           ->to('cedric.caty@free.fr')
            ->subject('Probl�me signal�')
            ->text("Un passager a signal� un probl�me sur le trajet {$booking->getRide()->getId()}: $comment");

        $mailer->send($email);

        // Marquer le trajet comme en litige
        $booking->getRide()->setStatus('pending_review');
        $em->flush();

        $this->addFlash('warning', 'Probl�me signal�, un employ� vous contactera bient�t.');
        return $this->redirectToRoute('my_bookings');
    }

    #[Route('/mes-voyages', name: 'app_mes_voyages')]
    #[IsGranted('ROLE_USER')]
    public function mesVoyages(Request $request, BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser();
    
        // V�rifier si l'utilisateur a s�lectionn� le r�le "passager" via une session ou un param�tre GET
        $session = $request->getSession();
        $isPassager = $session->get('role') === 'Passager';
        // dump($session->all()); 
        if (!$isPassager) {
            throw $this->createAccessDeniedException('Acc�s refus�. Vous devez �tre passager pour voir cette page.');
        }
    
        $dateDuJour = new \DateTime();
    
        // R�cup�ration des voyages du passager
        $voyagespasses = $bookingRepository->findByVoyagesPasses($user, $dateDuJour);
        $voyagesavenir = $bookingRepository->findByVoyagesAVenir($user, $dateDuJour);

        dd($voyagesavenir);
        dd($voyagespasses);
       
    
        return $this->render('ride/search_results.html.twig', [
            'voyagespasses' => $voyagespasses,
            'voyagesavenir' => $voyagesavenir,
        ]);
    }

 
}