<?php
// src/Controller/Admin/CrudController.php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use App\Entity\Note;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class NoteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Note::class;
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
        ;
    }
    public function configureFields(string $pageName): array|\Traversable
    {
        $id= IdField::new("id", "id");
        $location= AssociationField::new("location", "location");
        $service= AssociationField::new("service", "service");
        $note= NumberField::new("note", "note")->setRequired(true);
        $user= AssociationField::new("user", "user")->setRequired(true);
        return [$id, $location, $service, $note, $user];
        }
    }
    // ...
