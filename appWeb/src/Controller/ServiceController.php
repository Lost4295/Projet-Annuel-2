<?php

//src/Controller/ServiceController.php

namespace App\Controller;

use App\Entity\Service;
use App\Form\DevisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ServiceController extends AbstractController
{

    #[Route("/service", name: "services")]
    public function table(Request $request, EntityManagerInterface $em)
    {

        $nettoyage = $em->getRepository(Service::class)->findBy(["type" => Service::NETTOYAGE]);
        $electricite = $em->getRepository(Service::class)->findBy(["type" => Service::ELECTRICITE]);
        $plomberie = $em->getRepository(Service::class)->findBy(["type" => Service::PLOMBERIE]);
        $peinture = $em->getRepository(Service::class)->findBy(["type" => Service::PEINTURE]);
        $bricolage = $em->getRepository(Service::class)->findBy(["type" => Service::BRICOLAGE]);

        return $this->render('service.html.twig', [
            'nettoyage' => $nettoyage,
            'electricite' => $electricite,
            'plomberie' => $plomberie,
            'peinture' => $peinture,
            'bricolage' => $bricolage
        ]);
    }
}