<?php

namespace App\Controller\Admin;

use App\Entity\Employe;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use Vich\UploaderBundle\Form\Type\VichFileType;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Symfony\Component\HttpFoundation\File\File; 
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class EmployeCrudController extends AbstractCrudController
{
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
           // ArrayField::new('roles', 'Rôles')
            ];

        
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Employe) {
            return;
        }
    
        // Assurer que l'employé n'a que ROLE_EMPLOYE lors de la création
        $entityInstance->setRoles(['ROLE_EMPLOYE']);
    
        parent::persistEntity($entityManager, $entityInstance);
    }

    // public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    // {
    //     if (!$entityInstance instanceof Employe) {
    //         return;
    //     }

    //     $roles = $entityInstance->getRoles();
    //     if (!in_array('ROLE_EMPLOYE', $roles)) {
    //         $roles[] = 'ROLE_EMPLOYE';
    //         $entityInstance->setRoles($roles);
    //     }

    //     parent::updateEntity($entityManager, $entityInstance);
    // }
}
