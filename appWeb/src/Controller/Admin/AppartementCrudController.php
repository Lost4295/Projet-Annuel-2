<?php
// src/Controller/Admin/CrudController.php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use App\Entity\Appartement;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\Choice;

class AppartementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Appartement::class;
    }

    // ...

    public function configureFields(string $pageName): array|\Traversable
    {
        $id = IdField::new("id");
        $description = TextField::new("description");
        $shortDesc = TextField::new("shortDesc");
        $price = NumberField::new("price");
        $adress = TextField::new("adress");
        $nbRooms = NumberField::new("nbRooms");
        $note = NumberField::new("note");
        $state = ChoiceField::new("state");
        
        return [];
    }
}
