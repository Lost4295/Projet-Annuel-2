<?php
// src/Controller/Admin/CrudController.php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use App\Entity\Professionnel;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class ProfessionnelCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Professionnel::class;
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
    public function configureFields(string $pageName): array|\Traversable
    {
        $id = IdField::new("id", 'id');
        $responsable = AssociationField::new('responsable', "responsable")->setRequired(true);
        $services = AssociationField::new('services', "services");
        $appartements = AssociationField::new('appartements', "appartements");
        $societyName = TextField::new("societyName", "societyname")->setRequired(true);
        $siretNumber = TextField::new("siretNumber", "siretnumber")->setRequired(true)->setMaxLength(14);
        $societyAddress = TextField::new("societyAddress", "societyaddress")->setRequired(true);
        $city = TextField::new("city", "city")->setRequired(true);
        $postalCode = TextField::new("postalCode", "postalcode")->setRequired(true)->setMaxLength(5);
        $country = TextField::new("country", "country")->setRequired(true);
        $image = ImageField::new("image", "image")->setBasePath("/images/presta")->setUploadDir("/public/images/presta");
        $avgNote = NumberField::new("avgNote", "avgNote");
        $workDays = ChoiceField::new("workDays", "workDays");
        $startHour = TimeField::new("startHour", "startHour");
        $endHour = TimeField::new("endHour", "endHour");
        $devis = AssociationField::new("devis", "devis");
        $prestatype = ChoiceField::new("prestatype", "prestatype");
        $isVerified = BooleanField::new("isVerified", "isVerified");
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $responsable, $societyName, $siretNumber, $societyAddress, $city, $postalCode, $country, $services, $appartements];
        } else if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $responsable, $societyName, $siretNumber, $societyAddress, $city, $postalCode, $country, $services, $appartements, $image, $avgNote, $workDays, $startHour, $endHour, $devis, $prestatype, $isVerified];
        } else {
            return [$responsable, $societyName, $siretNumber, $societyAddress, $city, $postalCode, $country, $services, $appartements, $image, $avgNote, $workDays, $startHour, $endHour, $devis, $prestatype, $isVerified];
        }
    }

    // ...
}
