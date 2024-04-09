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
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class ProfessionnelCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Professionnel::class;
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }
    public function configureFields(string $pageName): array|\Traversable
    {
            $id = IdField::new("id", 'id');
            $responsable= AssociationField::new('responsable', "responsable")->setRequired(true);
            $services= AssociationField::new('services', "services");
            $societyName= TextField::new("societyName", "societyname")->setRequired(true);
            $siretNumber= TextField::new("siretNumber", "siretnumber")->setRequired(true)->setMaxLength(14);
            $societyAddress= TextField::new("societyAddress", "societyaddress")->setRequired(true);
            $city= TextField::new("city", "city")->setRequired(true);
            $postalCode= TextField::new("postalCode", "postalcode")->setRequired(true)->setMaxLength(5);
            $country= TextField::new("country", "country")->setRequired(true);
            if (Crud::PAGE_INDEX === $pageName) {
                return [$id, $responsable, $societyName, $siretNumber, $societyAddress, $city, $postalCode, $country, $services];
            } elseif(Crud::PAGE_DETAIL === $pageName) {
                return [$id, $responsable, $societyName, $siretNumber, $societyAddress, $city, $postalCode, $country, $services];
            } elseif(Crud::PAGE_EDIT === $pageName) {
                return [ $responsable, $societyName, $siretNumber, $societyAddress, $city, $postalCode, $country, $services];
            } elseif(Crud::PAGE_NEW === $pageName) {
                return [ $responsable, $societyName, $siretNumber, $societyAddress, $city, $postalCode, $country, $services];
            }
    }

    // ...
}
