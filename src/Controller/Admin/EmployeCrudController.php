<?php

namespace App\Controller\Admin;

use App\Entity\Employe;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
class EmployeCrudController extends AbstractCrudController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public static function getEntityFqcn(): string
    {
        return Employe::class;
    }
   
    public function configureFields(string $pageName): iterable
    {
            return [
            IdField::new('Id')->hideOnForm(),
            TextField::new('email', 'Email'),
            TextField::new('password', 'Mot de passe')->onlyOnForms(),
            TextField::new('pseudo', 'Pseudo'),
            ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Employe) {
            return;
        }
        // Récupérer le mot de passe en clair
        $plainPassword = $entityInstance->getPassword();
        $entityInstance->setRoles(['ROLE_EMPLOYE']);

        // Vérifier si un mot de passe a été fourni
        if ($plainPassword) {
            // Hacher le mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $plainPassword);
            // Assigner le mot de passe haché à l'entité
            $entityInstance->setPassword($hashedPassword);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

}
