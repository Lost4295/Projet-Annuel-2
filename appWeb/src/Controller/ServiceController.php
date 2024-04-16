<?php

//src/Controller/ServiceController.php

namespace App\Controller;

use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ServiceController extends AbstractController
{

    #[Route("/service", name: "services")]
    public function table(Request $request, EntityManagerInterface $em)
    {
        $services= $em->getRepository(Service::class)->findBy(["type" => "service"]);
        $produits = $em->getRepository(Service::class)->findBy(["type" => "produit"]);
        return $this->render('service.html.twig', [
            'services' => $services,
            'produits' => $produits
        ]);
    }
}