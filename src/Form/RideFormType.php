<?php

namespace App\Form;

use App\Entity\Ride;
use App\Entity\User;
use App\Entity\Vehicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RideFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $builder
        //     ->add('departure', TextType::class, ['label' => 'DÃ©part'])
        //     ->add('arrival', TextType::class, ['label' => 'ArrivÃ©e'])
        //     ->add('departureDay', DateTimeType::class, ['label' => 'Heure de dÃ©part'])
        //     ->add('price', MoneyType::class, ['label' => 'Prix'])
        //     ->add('availableSeats', IntegerType::class, ['label' => 'Places disponibles']);

        //$user = $options['user']; // RÃ©cupÃ©rer l'utilisateur connectÃ© pour afficher ses vÃ©hicules
        $vehicles = $options['vehicles'];

        $builder
            ->add('departure', TextType::class, [
                'label' => 'Ville de départ',
                'constraints' => [new NotBlank()],
            ])
            ->add('destination', TextType::class, [
                'label' => 'Ville d\'arrivée',
                'constraints' => [new NotBlank()],
            ])
            ->add('departureDay', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'constraints' => [new NotBlank()],
            ])
            ->add('departureTime', TimeType::class, [
                'label' => 'Heure de départ',
                
                'widget' => 'single_text',
                'constraints' => [new NotBlank()],
               
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix ',
                'currency' => 'EUR',
                'constraints' => [new NotBlank()],
            ])
          
            ->add('duration', TextType::class, [
                'label' => 'Durée (HH:mm)',
                
                'attr' => ['placeholder' => 'ex: 02:30'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une durée.']),
                    new Regex([
                        'pattern' => '/^\d{1,2}:\d{2}$/',
                        'message' => 'Le format doit être HH:mm (ex: 02:30).'
                    ]),
                ],
            ])
           
            ->add('vehicle', EntityType::class, [
                'label' => 'Véhicule',
                'class' => Vehicle::class,
                'choices' => $options['vehicles'], // Utilise l'option pour rÃ©cupÃ©rer les vÃ©hicules
                'choice_label' => function (Vehicle $vehicle) {
                    return $vehicle->getBrand() . ' - ' . $vehicle->getLicensePlate();
                },
                'placeholder' => 'Sélectionnez un véhicule',
                'required' => false,
            ]);
            // ->add('submit', SubmitType::class, ['label' => 'Proposer le voyage']);
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ride::class,
           // 'user' => null, // On passe l'utilisateur en option
            'vehicles' => [],
        ]);
    }
}
