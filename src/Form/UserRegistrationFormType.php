<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use App\Entity\Vehicle;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Form\VehicleFormType;

class UserRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer une adresse email.']),
                    new Assert\Email(['message' => 'L\'adresse email n\'est pas valide.']),
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer un nom.']),
                ],
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer votre pseudo.']),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
                'mapped' => true, // Empêche Doctrine de considérer ce champ comme une colonne en base de données
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer un mot de passe.']),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ],
            ])
            // ->add('userType', ChoiceType::class, [
            //     'label' => 'Type d\'utilisateur',
            //     'choices'  => [
            //         'Passager' => 'P',
            //         'Chauffeur' => 'C',
            //         'Chauffeur-Passager' => 'CP',
            //     ],
            //     'expanded' => true, 
            //     'multiple' => false,
            // ]);

        // Ajout dynamique du champ vehicles si nécessaire
        // $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
        //     $user = $event->getData();
        //     $form = $event->getForm();

        //     if ($user instanceof User && in_array($user->getUserType(), ['C', 'CP'])) {
        //         $form->add('vehicles', CollectionType::class, [
        //             'entry_type' => VehicleFormType::class,
        //             'allow_add' => true,
        //             'allow_delete' => true,
        //             'by_reference' => false,
        //             'mapped' => true, // Maintenant, les véhicules sont bien liés à l'entité User
        //             'label' => 'Véhicules',
        //         ]);
        //     }
        // });
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
