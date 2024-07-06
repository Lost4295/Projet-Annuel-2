<?php

namespace App\Controller;

use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

#[AsController]
class GetLocs extends AbstractController
{

    #[Route(
        name: 'get_locs_user',
        path: '/usrlocs/',
        methods: ['GET'],
        defaults: ['_api_resource_class' => 'App\Entity\Location', '_api_item_operation_name' => 'get'],
    )]

    public function userLocs( EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $locs = $em->getRepository(Location::class)->findBy(["locataire"=>$user]);
        $data = [];
        foreach ($locs as $loc ){
            $loco = [
                "id"=>$loc->getId(),
                "dateDebut"=>$loc->getDateDebut(),
                "dateFin"=>$loc->getDateFin(),
                "prix"=>$loc->getPrice(),
                "services"=>[],
                "appartement"=>$loc->getAppartement()->getImages()[0],
                "locataire"=>$loc->getLocataire()->getNom(),
                "adults"=>$loc->getAdults(),
                "kids"=>$loc->getKids(),
                "babies"=>$loc->getBabies(),
                "price"=>$loc->getPrice(),
            ];
            foreach ($loc->getServices() as $service){
                $loco["services"][] = [
                    "id"=>$service->getId(),
                    "titre"=>$service->getTitre(),
                    "description"=>$service->getDescription(),
                    "image" => $service->getImages()[0],
                ];
            }
            $data[] = $loco;
        }
        return $this->json(["data"=>$data]);
    }
}