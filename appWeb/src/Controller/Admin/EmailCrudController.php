<?php
// src/Controller/Admin/CrudController.php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use App\Entity\Email;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class EmailCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Email::class;
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
        $destinataire= TextField::new("destinataire","destinataire")->setRequired(true);
        $body= TextareaField::new("body","body")->setRequired(true);
        $pj= CollectionField::new("pj","pj");
        $object= TextField::new("object","object")->setRequired(true);
        $date= DateField::new("date","date")->setRequired(true);
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $object, $destinataire, $date];
        } elseif(Crud::PAGE_DETAIL === $pageName) {
            return [$id, $object, $destinataire, $body, $pj, $date];
        } else {
            return [$object, $destinataire, $body, $pj, $date];
        }
    }
    // ...
}
