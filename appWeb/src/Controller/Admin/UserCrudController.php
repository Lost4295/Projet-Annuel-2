<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    private $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new("id", "id");
        $email = EmailField::new("email", "email")->setRequired(true);
        $roles = ChoiceField::new("roles", "roles")
            ->allowMultipleChoices()
            ->setChoices(User::getPossibleRoles());
        $password = TextField::new("password", "password")->setRequired(true);
        $nom = TextField::new("nom", 'nom')->setRequired(true);
        $prenom = TextField::new("prenom", 'prenom')->setRequired(true);
        $lastConnDate = DateField::new("lastConnDate", 'lastcdate');
        $creationDate = DateField::new("creationDate", 'crdate');
        $admin = BooleanField::new("admin", "admin");
        $birthdate = DateField::new("birthdate", "birthdate")->setRequired(true);
        $isVerified = BooleanField::new("isVerified", "verified");
        $avatar = ImageField::new("avatar", "avatar")
            ->setUploadDir("var/uploads/avatars")
            ->setBasePath("uploads/avatars");
        $phoneNumber = TelephoneField::new("phoneNumber", 'phone')->setRequired(true);
        $abonnement = AssociationField::new("abonnement", 'abo')->setRequired(true);
        $tickets = AssociationField::new("tickets", 'tickets')->setRequired(false);
        $ticketsAttribues = AssociationField::new("ticketsAttribues", 'ticketsa')->setRequired(false);
        $locations = AssociationField::new("locations", 'locations')->setRequired(false);

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $nom, $prenom, $roles, $lastConnDate, $creationDate, $admin, $birthdate, $isVerified, $phoneNumber];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $nom, $prenom, $roles, $lastConnDate, $creationDate, $admin, $birthdate, $isVerified, $avatar, $phoneNumber, $abonnement, $locations, $tickets, $ticketsAttribues];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$email, $roles, $nom, $prenom, $admin, $birthdate, $isVerified, $avatar, $phoneNumber, $abonnement, $locations, $tickets, $ticketsAttribues];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$email, $roles, $password, $nom, $prenom, $admin, $birthdate, $isVerified, $avatar, $phoneNumber, $abonnement];
        }
    }
    
}
