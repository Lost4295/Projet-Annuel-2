<?php
// src/Controller/Admin/CrudController.php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use App\Entity\Service;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ServiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Service::class;
    }

    // ...
    public function configureFields(string $pageName): array|\Traversable
    {
        $id = IdField::new("id", 'id');
        $titre = TextField::new("titre", 'title');
        $description = TextareaField::new("description", 'description');
        $type = ChoiceField::new("type", 'type')->setChoices(["service" => "service", "produit" => "produit"]);
        $prestataire = CollectionField::new("prestataire", 'prestataire');
        $tarifs = MoneyField::new("tarifs", 'tarif')->setCurrency("EUR");
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $titre, $description, $type, $prestataire, $tarifs];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $titre, $description, $type, $prestataire, $tarifs];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$titre, $description, $type, $prestataire, $tarifs];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$titre, $description, $type, $prestataire, $tarifs];
        }
    }
}
