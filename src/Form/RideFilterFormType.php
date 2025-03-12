<?php

namespace App\Form;

use App\Entity\Ride;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RideFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('departure', TextType::class, [
                'required' => false,
                'label' => 'Départ',
            ])
            ->add('destination', TextType::class, [
                'required' => false,
                'label' => 'Destination',
            ])
            ->add('departureDay', DateType::class, [
                'required' => false,
               'widget' => 'single_text',
                'label' => 'Date de départ',
               
            ])
            ->add('availableSeats', IntegerType::class, [
                'label' => 'Places disponibles',
                'mapped' => false, // Ne sera pas lié à un champ de l'entité Ride directement
                'attr' => ['style' => 'display:none;'], // Masquer le champ dans le formulaire
            ]);
          
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ride::class,
            'method' => 'GET',
        ]);
    }
}
