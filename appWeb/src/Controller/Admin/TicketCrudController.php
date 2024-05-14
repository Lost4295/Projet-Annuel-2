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
use App\Entity\Ticket;
use App\Form\EmailType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class TicketCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ticket::class;
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
        ;
    }
    public function configureFields(string $pageName): array|\Traversable
    {
        $id= IdField::new("id","id");
        $titre = TextField::new("titre", "titre")->setRequired(true);
        $dateOuverture= DateField::new("dateOuverture","dateOuverture");
        $dateFermeture= DateField::new("dateFermeture","dateFermeture");
        $demandeur= AssociationField::new("demandeur","demandeur");
        $lastUpdateDate= DateField::new("lastUpdateDate","lastUpdateDate");
        $category= ChoiceField::new("category","category")->setChoices(array_flip(Ticket::CATEGORY_LIST));
        $type= ChoiceField::new("type","type")->setChoices(array_flip(Ticket::TYPE_LIST));
        $status= ChoiceField::new("status","status")->setChoices(array_flip(Ticket::STATUS_LIST));
        $priority= ChoiceField::new("priority","priority")->setChoices(array_flip(Ticket::PRIORITY_LIST));
        $description= TextareaField::new("description","description");
        $pj= AssociationField::new("pj","pj");
        $urgence= ChoiceField::new("urgence","urgence")->setChoices(array_flip(Ticket::URGENCE_LIST));
        $resolveur= AssociationField::new("resolveur","resolveur");
        
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $titre, $dateOuverture, $dateFermeture, $demandeur, $category, $type, $status, $priority, $urgence, $resolveur];
        } elseif(Crud::PAGE_DETAIL === $pageName) {
        return [$id, $titre, $dateOuverture, $dateFermeture, $demandeur, $lastUpdateDate, $category, $type, $status, $priority, $description, $pj, $urgence, $resolveur];
        } elseif(Crud::PAGE_EDIT === $pageName) {
        return [$titre, $dateOuverture, $dateFermeture, $demandeur, $lastUpdateDate, $category, $type, $status, $priority, $description, $pj, $urgence, $resolveur];
        } elseif(Crud::PAGE_NEW === $pageName) {
        return [$titre, $dateOuverture, $dateFermeture, $demandeur, $category, $type, $status, $priority, $description, $pj, $urgence, $resolveur];
        }
    }
    // ...
}
