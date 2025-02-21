<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Vehicle;

class VehicleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    $builder
    ->add('licensePlate', TextType::class, ['label' => 'Plaque d\'immatriculation'])
    ->add('dateFirstUse', DateType::class, [
        'widget' => 'single_text',
        'label' => 'Date de première immatriculation'
    ])
    ->add('model', TextType::class, ['label' => 'Modèle'])
    ->add('color', TextType::class, ['label' => 'Couleur'])
    ->add('brand', TextType::class, ['label' => 'Marque'])
    ->add('seatscount', IntegerType::class, ['label' => 'Nombre de places disponibles'])
    ->add('energy', ChoiceType::class, [
        'label' => 'Type d?énergie',
        'choices' => [
            'Électrique' => 'Electrique',
            'Hybride' => 'Hybride',
            'Essence' => 'Essence',
            'Gasoil' => 'Gasoil',
            'Gaz' => 'Gaz',
            'Hydrogène' => 'Hydrogène',
        ],
        'required' => true,
    ])
    ->add('preferences', ChoiceType::class, [
        'choices' => [
            'Fumeur' => 'fumeur',
            'Non-fumeur' => 'non_fumeur',
            'Animaux acceptés' => 'animaux_acceptes',
            'Pas d\'animaux' => 'pas_animaux',
        ],
            'expanded' => true,
            'multiple' => true,
            'label' => 'Préférences',
         ])
    // ->add('customPreferences',CollectionType::class, [
    //     'entry_type' => TextType::class,
    //     'allow_add' => true,
    //     'allow_delete' => true,
    //     'label' =>'Ajouter vos propres préférences',
    //     'mapped' => false,
    //     'entry_options' =>['attr'=>['class' => 'custom-preference']]
    // ])
   
    ->add('save', SubmitType::class, ['label' => 'Sauvegarder le véhicule'])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicle::class,
        ]);
    }
}