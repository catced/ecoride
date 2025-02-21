<?php

namespace App\Controller\Admin;

use App\Entity\Inscription;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Membre;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;



class InscriptionCrudController extends AbstractCrudController
{
    private UrlGeneratorInterface $urlGenerator; // Utilisez UrlGeneratorInterface

    // Injection du service UrlGeneratorInterface
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Inscription::class;
    }

    
    // public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, QueryBuilder $queryBuilder): QueryBuilder
    // {
    //     $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $queryBuilder);
        
    //     // Filtrer uniquement les inscriptions non validées
    //     $qb->andWhere('inscription.estValide = :valide')
    //        ->setParameter('valide', false);

    //     return $qb;
    // }

    // public function configureFields(string $pageName): iterable
    // {
    //     return [
    //         TextField::new('nomadherent', 'Nom'),
    //         TextField::new('prenom', 'Prénom'),
    //         TextField::new('email', 'Email'),
    //         TextField::new('telephone', 'Téléphone'),
    //         BooleanField::new('estValide', 'Validée'),
    //         BooleanField::new('estValide', 'Transformer en membre')
    //             ->renderAsSwitch(false)  // Utilise une case à cocher
    //             ->setFormTypeOption('disabled', false), // Permet de cocher/décocher
    //     ];
    // }
    // public function createIndexQueryBuilder(QueryBuilder $queryBuilder, string $entityClass, string $sortDirection, ?string $sortField = null): QueryBuilder
    // {
    //     // Crée un QueryBuilder basé sur le parent
    //     $qb = parent::createIndexQueryBuilder($queryBuilder, $entityClass, $sortDirection, $sortField);
        
    //     // Ajouter un filtre pour les inscriptions non validées
    //     $qb->andWhere('entity.estValide = :valide')
    //        ->setParameter('valide', false);

    //     return $qb;
    // }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // Filtrer pour ne récupérer que les inscriptions non validées (dateValidation est null)
        $qb->andWhere('entity.estValide = 0');

        return $qb;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Inscription')
            ->setEntityLabelInPlural('Inscriptions non validées')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $validateAction = Action::new('valider', 'Valider')
            ->linkToCrudAction('validerInscription')
            ->addCssClass('btn btn-success');

        return $actions
            ->add(Crud::PAGE_INDEX, $validateAction);

        // $validateAction = Action::new('valider', 'Valider')
        // ->linkToCrudAction('validerInscription')  // Lier à l'action de validation
        // ->addCssClass('btn btn-success')         // Ajouter des classes CSS pour le style du bouton
        // ->displayIf(static function (Inscription $inscription) {
        //     // Afficher le bouton uniquement si l'inscription n'est pas encore validée
        //     return !$inscription->getEstValide();
        // });

        // // Ajouter l'action à la liste des actions disponibles sur la page d'index
        // return $actions
        //     ->add(Crud::PAGE_INDEX, $validateAction);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nomAdherent', 'Nom'),
            TextField::new('prenom', 'Prénom'),
            TextField::new('email', 'Email'),
            TextField::new('telephone', 'Téléphone'),
            Field::new('montantTotal', 'Montant Total')
                ->setCustomOption('format', function ($value) {
                    // Vérifie si la valeur est nulle et formate avec deux décimales
                    return $value !== null ? number_format((float)$value, 2, ',', ' ') : '0';
                })
                // ->setSortable(true),
            // BooleanField::new('estValide', 'Validée'),
        ];
    }

    // public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    // {
    //     if ($entityInstance instanceof Inscription && $entityInstance->getEstValide()) {
    //         // Créer un membre à partir de l'inscription validée
    //         $membre = new Membre();
    //         $membre->setNom($entityInstance->getNomAdherent());
    //         $membre->setPrenom($entityInstance->getPrenom());
    //         $membre->setEmail($entityInstance->getEmail());
    //         $membre->setTelephone($entityInstance->getTelephone());
    //         $membre->setPassword($entityInstance->getPassword());

    //         // Sauvegarder le nouveau membre
    //         $entityManager->persist($membre);

    //         // Supprimer l'inscription une fois validée
    //         $entityManager->remove($entityInstance);
    //     } else {
    //         // Sinon, simplement sauvegarder l'inscription
    //         $entityManager->persist($entityInstance);
    //     }

    //     $entityManager->flush();
    // }
 


    public function validerInscription(AdminContext $context, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): RedirectResponse
    {
        $inscription = $context->getEntity()->getInstance();

        if ($inscription instanceof Inscription) {
            // Créer un membre à partir de l'inscription validée
            $membre = new Membre();
            $membre->setNom($inscription->getNomAdherent());
            $membre->setPrenom($inscription->getPrenom());
            $membre->setEmail($inscription->getEmail());
            $membre->setTelephone($inscription->getTelephone());
            $membre->setPassword($inscription->getPassword());

            $membre->setActif(true);
            $membre->setRoles(["ROLE_USER"]);
            

            $entityManager = $doctrine->getManager();    //getDoctrine()->getManager();
            

            // Supprimer l'inscription après validation
            $inscription->setEstValide(true);
            $montantTotal = $inscription->getMontantTotal() ?? 0;
            // dd($montantTotal);
            $membre->setMontantTotal($montantTotal);
            $entityManager->persist($membre);
            $entityManager->flush();

            $entityManager->remove($inscription);
            // Ajouter un message flash pour indiquer la réussite
            $this->addFlash('success', 'Inscription validée et transférée dans les membres.');

            // Redirection vers la liste des inscriptions non validées
            $url = $this->urlGenerator->generate('admin', [
                'crudAction' => 'index',
                'crudControllerFqcn' => self::class,
            ]);
            return $this->redirect($url);
        }
             
        $this->addFlash('error', 'Erreur lors de la validation de l\'inscription.');
        $url = $this->urlGenerator->generate('admin', [
            'crudAction' => 'index',
            'crudControllerFqcn' => self::class,
        ]);
        return $this->redirect($url);
    }
   
}
