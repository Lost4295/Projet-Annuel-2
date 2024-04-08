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
        $id= IdField::new("id");
        $destinataire= TextField::new("destinataire");
        $body= TextareaField::new("body");
        $cc= TextField::new("cc");
        $pj= CollectionField::new("pj");
        $object= TextField::new("object");
        $isAutomatic= BooleanField::new("isAutomatic");
        $date= DateField::new("date");
        return [];
    }
    // ...
}
