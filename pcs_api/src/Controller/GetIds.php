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
        $user = $this->getUser();
        return $this->json(["data"=>[
            "id"=>$user->getId(),
            "name"=>$user->getNom(),
            "firstname"=>$user->getPrenom(),
            "email"=>$user->getUserIdentifier(),
            "phone"=>$user->getPhoneNumber(),
            "birthdate"=>$user->getBirthdate()->format('d-m-Y'),
            "abonnement"=>$user->getAbonnement()->__toString(),
            ]]);
    }
}