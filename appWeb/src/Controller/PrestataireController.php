<?php

//src/Controller/PrestataireController.php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route("/prestataires")]
class PrestataireController extends AbstractController
{
    #[Route("/", name: "prestataires")]
    public function prestataires()
    {
        return $this->render('presta/index.html.twig', [
        ]);
    }
}