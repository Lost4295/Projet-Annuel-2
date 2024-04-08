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
use Symfony\Component\DomCrawler\Field\FileFormField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }




    public function configureFields(string $pageName): iterable
    {
$id= IdField::new("id");
$email= TextField::new("email");
$roles= ChoiceField::new("roles");
$password= TextField::new("password");
$nom= TextField::new("nom");
$prenom= TextField::new("prenom");
$lastConnDate= DateField::new("lastConnDate");
$creationDate= DateField::new("creationDate");
$admin= BooleanField::new("admin");
$birthdate= DateField::new("birthdate");
$isVerified= BooleanField::new("isVerified");
$avatar= ImageField::new("avatar");
$phoneNumber= TextField::new("phoneNumber");
$professionnel= CollectionField::new("professionnel");
        return [
            IdField::new('id'),
            TextField::new('nom'),
            TextField::new('prenom'),
            TextField::new('email'),
            DateField::new('creationDate'),
            DateField::new('lastConnDate'),
            BooleanField::new('isVerified'),
            BooleanField::new('admin'),
        ];
    }

}
