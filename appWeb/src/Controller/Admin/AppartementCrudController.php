<?php
// src/Controller/Admin/CrudController.php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use App\Entity\Appartement;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class AppartementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Appartement::class;
    }

    // ...
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }
    public function configureFields(string $pageName): array|\Traversable
    {
        $id = IdField::new("id", "id");
        $description = TextareaField::new("description", "description");
        $shortDesc = TextField::new("shortDesc", "shdesc");
        $price = MoneyField::new("price", "price")->setCurrency("EUR")->setCustomOption('storedAsCents', false);
        $adress = TextField::new("address", "address");
        $nbRooms = NumberField::new("nbRooms", "nbrooms");
        $note = NumberField::new("note", "note");
        $state = ChoiceField::new("state", "state")->setChoices(["Disponible" => "Disponible", "En attente" => "En attente", "Loué" => "Loué"]); // TODO : faire une fonction pour récup ça dans le fichier de config
        $bailleur= AssociationField::new('bailleur', "baill")->setRequired(true);
        $photos = ImageField::new('appartement', "photos")->setRequired(true)->setUploadDir("/public/images/appartements")->setBasePath("/images/appartements");
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $shortDesc, $price, $adress, $nbRooms, $note, $state, $bailleur];
        } elseif(Crud::PAGE_DETAIL === $pageName) {
            return [$id, $description, $shortDesc, $price, $adress, $nbRooms, $note, $state, $bailleur];
        } else {
            return [$shortDesc, $description, $price, $adress, $nbRooms, $state, $bailleur];
        }

    }
}
