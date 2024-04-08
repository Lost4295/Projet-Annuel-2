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
        $id= IdField::new("id");
        $nom= TextField::new("nom");
        $type= ChoiceField::new("type");
        $path= TextField::new("path");
        $email= CollectionField::new("email");
        return [];
    }
    // ...
}
