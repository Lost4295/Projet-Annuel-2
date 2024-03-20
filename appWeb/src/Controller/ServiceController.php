<?php

//src/Controller/ServiceController.php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ServiceController extends AbstractController
{

    #[Route("/service", name: "services")]
    public function table()
    {
        return $this->render('service.html.twig', [
        ]);
    }
}