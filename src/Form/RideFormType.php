<?php

namespace App\Form;

use App\Entity\Ride;
use App\Entity\User;
use App\Entity\Vehicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;

class RideFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $builder
        //     ->add('departure', TextType::class, ['label' => 'Départ'])
        //     ->add('arrival', TextType::class, ['label' => 'Arrivée'])
        //     ->add('departureDay', DateTimeType::class, ['label' => 'Heure de départ'])
        //     ->add('price', MoneyType::class, ['label' => 'Prix'])
        //     ->add('availableSeats', IntegerType::class, ['label' => 'Places disponibles']);

        //$user = $options['user']; // Récupérer l'utilisateur connecté pour afficher ses véhicules
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
            ->add('departureDay', DateTimeType::class, [
                'label' => 'Date et heure',
                'widget' => 'single_text',
                'constraints' => [new NotBlank()],
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix ',
                'currency' => 'EUR',
                'constraints' => [new NotBlank()],
            ])
            ->add('duration', NumberType::class, [
                'label' => 'Durée (minutes)',
                'constraints' => [new NotBlank()],
            ])
            // ->add('vehicle', EntityType::class, [
            //     'class' => Vehicle::class,
            //     //'choices' => $user->getVehicles(), // Liste des véhicules de l'utilisateur
            //     'choices' => $vehicles,
            //     'choice_label' => function (Vehicle $vehicle) {
            //         return $vehicle->getBrand() . ' - ' . $vehicle->getLicensePlate();
            //     },
            //     'placeholder' => 'Sélectionnez un véhicule existant',
            //     'required' => false,
            // ])
            ->add('vehicle', EntityType::class, [
                'class' => Vehicle::class,
                'choices' => $options['vehicles'], // Utilise l'option pour récupérer les véhicules
                'choice_label' => function (Vehicle $vehicle) {
                    return $vehicle->getBrand() . ' - ' . $vehicle->getLicensePlate();
                },
                'placeholder' => 'Sélectionnez un véhicule',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Proposer le voyage']);
    
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
