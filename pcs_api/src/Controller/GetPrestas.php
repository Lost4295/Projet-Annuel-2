<?php

namespace App\Controller;

use App\Entity\Professionnel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

#[AsController]
class GetPrestas extends AbstractController
{

    #[Route(
        name: 'getallprestas',
        path: '/getprestas/',
        methods: ['GET'],
        defaults: ['_api_resource_class' => 'App\Entity\Professionnel', '_api_item_operation_name' => 'get'],
    )]
    public function tot(EntityManagerInterface $em): Response
    {
        $presta = $em->getRepository(Professionnel::class)->findAll();
        foreach ($presta as $prest ){
            if (!in_array("ROLE_PRESTA",$prest->getResponsable()->getRoles())){
                unset($presta[array_search($prest,$presta)]);
            }
        }

        return $this->json(["data"=>array_values($presta)]);
    }
}