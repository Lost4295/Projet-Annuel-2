<?php
// src/Controller/Admin/AbonnementController.php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AbonnementController extends AbstractController
{
    #[Route("/tarifications",name: "tarifs")]
    public function makeEmailer(Request $request): Response
    {

        return $this->render('admin/tarifs.html.twig', [
        ]);
    }
    // ...
}
