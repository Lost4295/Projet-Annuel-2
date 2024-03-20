<?php
// src/Controller/Admin/TraitementController.php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;


class TraitementController extends AbstractController
{
    private $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    #[Route("/states", name: "all_states")]
    public function index(Request $request): Response
    {
        $url = $this->adminUrlGenerator->setRoute('action_state')->generateUrl();
        return $this->render('admin/traitements.html.twig', ['url'=>$url]);
    }
    #[Route("/omh", name: "action_state")]
    public function specific(Request $request): Response
    {
        return $this->render('admin/traitement.html.twig', [
        ]);
    }
    // ...
}
