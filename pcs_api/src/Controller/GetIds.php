<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

#[AsController]
class GetIds extends AbstractController
{

    #[Route(
        name: 'current_user_get',
        path: '/user/',
        methods: ['GET'],
        defaults: ['_api_resource_class' => 'App\Entity\User', '_api_item_operation_name' => 'get'],
    )]
    public function tot(): Response
    {
        return $this->json(["data"=>[$this->getUser()->getId(),$this->getUser()->getUserIdentifier()]]);
    }
}