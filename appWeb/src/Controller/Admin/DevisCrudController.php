<?php
// src/Controller/Admin/CrudController.php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use App\Entity\Devis;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class DevisCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Devis::class;
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
        $id= IdField::new("id", 'id');
        $nom= TextField::new("nom","nom")->setRequired(true);
        $prenom= TextField::new("prenom","prenom")->setRequired(true);
        $numero= TextField::new("numero","numero");
        $email= TextField::new("email","email")->setRequired(true);
        $typePresta= ChoiceField::new("typePresta","typePresta")->setRequired(true)->setChoices(Devis::getTypePrestaString());
        $description= TextField::new("description","description");
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $nom, $prenom, $numero, $email, $typePresta];
        } else {
            return [$id, $nom, $prenom, $numero, $email, $typePresta, $description];
        }
    }
    // ...
}
