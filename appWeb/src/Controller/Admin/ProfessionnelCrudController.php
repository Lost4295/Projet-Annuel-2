<?php
// src/Controller/Admin/CrudController.php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use App\Entity\Professionnel;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProfessionnelCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Professionnel::class;
    }

    public function configureFields(string $pageName): array|\Traversable
    {
        
            $responsable= AssociationField::new('responsable')->setRequired(true),
            $services= AssociationField::new('services'),
            $societyName= TextField::new("societyName")->setRequired(true),
            $siretNumber= TextField::new("siretNumber")->setRequired(true)->setMaxLength(14),
            $societyAddress= TextField::new("societyAddress")->setRequired(true),
            $city= TextField::new("city")->setRequired(true),
            $postalCode= TextField::new("postalCode")->setRequired(true)->setMaxLength(5),
            $country= TextField::new("country")->setRequired(true),
            return [
        ];
    }

    // ...
}
