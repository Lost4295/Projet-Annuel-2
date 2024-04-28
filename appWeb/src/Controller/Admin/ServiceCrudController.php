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
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class ServiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Service::class;
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }
    // ...
    public function configureFields(string $pageName): array|\Traversable
    {
        $id = IdField::new("id", 'id');
        $titre = TextField::new("titre", 'title')->setRequired(true);
        $description = TextareaField::new("description", 'description')->setRequired(true)->setMaxLength(520);
        $type = ChoiceField::new("type", 'type')->setChoices(Service::getTypes())->setRequired(true);
        $prestataire = AssociationField::new("prestataire", 'prestataire')->setRequired(true);
        $tarifs = MoneyField::new("tarifs", 'tarif')->setCurrency("EUR")->setCustomOption('storedAsCents', false)->setRequired(true);
        if (Crud::PAGE_INDEX === $pageName || Crud::PAGE_DETAIL === $pageName) {
            return [$id, $titre, $description, $type, $prestataire, $tarifs];
        } else {
            return [$titre, $description, $type, $prestataire, $tarifs];
        }
    }
}
