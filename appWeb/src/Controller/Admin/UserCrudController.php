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

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }




    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new("id", "id");
        $email = EmailField::new("email", "email");
        $roles = ChoiceField::new("roles", "roles")->allowMultipleChoices()->setChoices(["user" => "ROLE_USER", "admin" => "ROLE_ADMIN"]); // TODO : faire une fonction pour récup ça dans le fichier de config
        $password = TextField::new("password", "password");
        $nom = TextField::new("nom", 'nom');
        $prenom = TextField::new("prenom", 'prenom');
        $lastConnDate = DateField::new("lastConnDate", 'lastcdate');
        $creationDate = DateField::new("creationDate", 'crdate');
        $admin = BooleanField::new("admin", "admin");
        $birthdate = DateField::new("birthdate", "birthdate");
        $isVerified = BooleanField::new("isVerified", "verified");
        $avatar = ImageField::new("avatar", "avatar")->setUploadDir("/var/uploads/avatars")->setBasePath("uploads/avatars");
        $phoneNumber = TelephoneField::new("phoneNumber", 'phone');
        $abonnement = AssociationField::new("abonnement", 'abo');
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $nom, $prenom, $roles, $lastConnDate, $creationDate, $admin, $birthdate, $isVerified, $phoneNumber, $abonnement];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $nom, $prenom, $roles, $lastConnDate, $creationDate, $admin, $birthdate, $isVerified, $avatar, $phoneNumber, $abonnement];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$email, $roles, $nom, $prenom, $admin, $birthdate, $isVerified, $avatar, $phoneNumber, $abonnement];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$email, $roles, $password, $nom, $prenom, $admin, $birthdate, $isVerified, $avatar, $phoneNumber, $abonnement];
        }
    }
}
