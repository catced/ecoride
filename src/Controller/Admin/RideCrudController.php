<?php

namespace App\Controller\Admin;

use App\Entity\Ride;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class RideCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ride::class;
    }

    // Vous pouvez personnaliser les pages de CRUD si nécessaire
}
