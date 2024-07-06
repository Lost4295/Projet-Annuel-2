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
                "date_debut"=>$loc->getDateDebut(),
                "date_fin"=>$loc->getDateFin(),
                "prix"=>$loc->getPrice(),
                "services"=>[],
                "appartemnt"=>$loc->getAppartement()->getImages()[0],
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