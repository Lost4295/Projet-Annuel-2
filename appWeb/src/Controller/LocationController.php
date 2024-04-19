<?php

//src/Controller/LocationController.php

namespace App\Controller;

use App\Entity\Appartement;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class LocationController extends AbstractController
{
    #[Route("/locations", name: "locations")]
    public function createPage(Request $request, EntityManagerInterface $em)
    {
        $routeName = $request->attributes->get("_route");

        if ($routeName === "number") {
            $number = random_int(0, 100);
        } else {
            $number = $routeName;
        }
        $apparts = $em->getRepository(Appartement::class)->findAll();
        return $this->render('locations.html.twig', [
            'text' => $number,
            'apparts' => $apparts
        ]);
    }

    #[Route("/locations/{id}", name: "appart_detail")]
    public function show($id, EntityManagerInterface $em)
    {
        $appart = $em->getRepository(Appartement::class)->find($id);
        return $this->render('appart_detail.html.twig', [
            'appart' => $appart
        ]);
    }

    
}