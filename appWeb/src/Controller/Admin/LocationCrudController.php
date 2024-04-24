<?php
// src/Controller/Admin/CrudController.php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use App\Entity\Location;
use App\Form\EmailType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class LocationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Location::class;
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
        $dateDebut= DateField::new("dateDebut", "dateDebut");
        $dateFin= DateField::new("dateFin", "dateFin");
        $appartement= AssociationField::new("appartement","appartement");
        $locataire= AssociationField::new("locataire","locataire");
        $adults= NumberField::new("adults", "adults");
        $kids= NumberField::new("kids", "kids");
        $babies= NumberField::new("babies", "babies");
        $price= MoneyField::new("price", "price")->setCurrency("EUR")->setCustomOption('storedAsCents', false);
        $services= AssociationField::new("services","services" );
        $id= IdField::new("id", "id");
        if (Crud::PAGE_INDEX === $pageName || Crud::PAGE_DETAIL === $pageName) {
            return [$id, $dateDebut, $dateFin, $appartement, $locataire, $adults, $kids, $babies, $price, $services];
        } else {
            return [$dateDebut, $dateFin, $appartement, $locataire, $adults, $kids, $babies, $price, $services];
        }
    }
    // ...
}
