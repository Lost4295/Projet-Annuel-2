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
use App\Entity\Fichier;
use App\Form\EmailType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FichierCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Fichier::class;
    }

    public function configureFields(string $pageName): array|\Traversable
    {
        $id= IdField::new("id", "id");
        $nom= TextField::new("nom", "title");
        $type= ChoiceField::new("type", "type")->setChoices(["image" => "image", "pdf" => "pdf", "word" => "word", "excel" => "excel", "powerpoint" => "powerpoint", "autre" => "autre"]);
        $path= TextField::new("path", "path");
        //$email= CollectionField::new("email")->setEntryType(EmailType::class)->setEntryIsComplex();
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $nom, $type];
        } elseif(Crud::PAGE_DETAIL === $pageName) {
        return [$id, $nom, $type, $path/*, $email*/];
        } elseif(Crud::PAGE_EDIT === $pageName) {
        return [$nom, $type, $path/*, $email*/];
        } elseif(Crud::PAGE_NEW === $pageName) {
        return [$nom, $type, $path/*, $email*/];
        }
    }
    // ...
}
