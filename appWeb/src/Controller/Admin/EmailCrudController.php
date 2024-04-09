<?php
// src/Controller/Admin/CrudController.php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use App\Entity\Email;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EmailCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Email::class;
    }

    public function configureFields(string $pageName): array|\Traversable
    {
        $id= IdField::new("id", 'id');
        $destinataire= TextField::new("destinataire","destinataire");
        $body= TextareaField::new("body","body");
        $cc= TextField::new("cc","cc");
        $pj= CollectionField::new("pj","pj");
        $object= TextField::new("object","object");
        $isAutomatic= BooleanField::new("isAutomatic","auto");
        $date= DateField::new("date","date");
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $object, $destinataire, $cc, $isAutomatic, $date];
        } elseif(Crud::PAGE_DETAIL === $pageName) {
            return [$id, $object, $destinataire, $body, $cc, $pj, $isAutomatic, $date];
        } elseif(Crud::PAGE_EDIT === $pageName) {
            return [$object, $destinataire, $body, $cc, $pj, $isAutomatic, $date];
        } elseif(Crud::PAGE_NEW === $pageName) {
            return [$object, $destinataire, $body, $cc, $pj, $isAutomatic, $date];
        }
    }
    // ...
}
