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
        return $this->json(["data"=>$locs]);
    }
}