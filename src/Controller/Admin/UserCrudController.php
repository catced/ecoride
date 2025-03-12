<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserCrudController extends AbstractCrudController
{
    private $passwordHasher;

    // Injectez le service de hashage de mot de passe
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            return;
        }

        // Assigner automatiquement le rÃ´le ROLE_ADMIN Ã  l'utilisateur
        $entityInstance->setRoles(['ROLE_ADMIN']);

        // SÃ©curiser le mot de passe
        $plainPassword = $entityInstance->getPassword();
        if ($plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $plainPassword);
            $entityInstance->setPassword($hashedPassword);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('username', 'Nom d\'utilisateur'),
            TextField::new('password', 'Mot de passe')->onlyOnForms(),
            // BooleanField::new('estactif', 'Est actif '),
            TextField::new('email', 'Email')
        ];

        $fields = [
            // Autres champs de l'entité User
            // Par exemple, pour l'email, le pseudo, etc.
        ];
    
        // Vérifier si l'utilisateur est un ADMIN
        if ($this->isGranted('ROLE_ADMIN')) {
            // Ajouter la case à cocher pour suspendre l'utilisateur
            $fields[] = BooleanField::new('isSuspended', 'Suspendu')
                ->setHelp('Cochez cette case pour suspendre l\'utilisateur');
        } else {
            // Si l'utilisateur n'est pas ADMIN, ne pas afficher le champ 'isSuspended'
            $fields[] = BooleanField::new('isSuspended', 'Suspendu')->onlyOnDetail(); // Affiche juste en lecture seule
        }
    
        return $fields;
    }

    
}
