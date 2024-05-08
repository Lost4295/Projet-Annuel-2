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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
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
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }
    public function configureFields(string $pageName): array|\Traversable
    {
        $id = IdField::new("id", "id");
        $description = TextareaField::new("description", "description")->setRequired(true);
        $shortDesc = TextField::new("shortDesc", "shdesc")->setMaxLength(120)->setRequired(true);
        $price = MoneyField::new("price", "price")->setCurrency("EUR")->setCustomOption('storedAsCents', false)->setRequired(true);
        $adress = TextField::new("address", "address")->setRequired(true);
        $nbRooms = NumberField::new("nbvoyageurs", "nbrooms")->setRequired(true);
        $note = NumberField::new("note", "note");
        $state = ChoiceField::new("state", "state")->setChoices(["Disponible" => "Disponible", "En attente" => "En attente", "Loué" => "Loué"])->setRequired(false); // TODO : faire une fonction pour récup ça dans le fichier de config
        $bailleur = AssociationField::new('bailleur', "baill")->setRequired(true);
        $photos = AssociationField::new('images', "photos")->setRequired(false);
        $pluses = AssociationField::new('appartPluses', "pluses");
        $city = TextField::new("city", "city")->setRequired(true);
        $postalCode = TextField::new("postalCode", "postalCode")->setRequired(true);
        $country = TextField::new("country", "country")->setRequired(true);
        $titre = TextField::new("titre", "titre")->setRequired(true);
        $nbchambers = NumberField::new("nbchambers", "nbchambers")->setRequired(true);
        $nbbathrooms = NumberField::new("nbbathrooms", "nbbathrooms")->setRequired(true);
        $nbBeds = NumberField::new("nbBeds", "nbBeds")->setRequired(true);
        $createdAt = DateField::new("createdAt", "createdAt")->setRequired(true);
        $updatedAt = DateField::new("updatedAt", "updatedAt")->setRequired(false);
        $surface = NumberField::new("surface", "surface")->setRequired(true);
        $locations = AssociationField::new("locations", "locations")->setRequired(false);
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $titre, $shortDesc, $price, $adress, $nbRooms, $note, $state, $bailleur, $pluses];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [
                $id, $titre, $description, $shortDesc, $price, $adress, $city, $postalCode, $country, $nbRooms, $nbchambers,
                $nbbathrooms, $nbBeds, $createdAt, $updatedAt, $surface, $note, $state, $bailleur, $photos, $pluses, $locations
            ];
        } else {
            return [
                $titre, $description, $shortDesc, $price, $adress, $city, $postalCode, $country, $nbRooms, $nbchambers,
                $nbbathrooms, $nbBeds, $createdAt, $updatedAt, $surface, $state, $bailleur, $photos, $pluses, $locations
            ];
        }
    }
}
