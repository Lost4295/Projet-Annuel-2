<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class APIController extends AbstractController
{

    #[Route("/apis", name: "api_state")]

    public function index(): Response
    {
        $apis = [
            ["id" => 1, "name" => "jfh", "state" => "ok"],
            ["id" => 2, "name" => "jfh", "state" => "ok"],
            ["id" => 3, "name" => "jfh", "state" => "ok"],
            ["id" => 4, "name" => "jfh", "state" => "nok"],
            ["id" => 5, "name" => "jfh", "state" => "ok"],
            ["id" => 6, "name" => "jfh", "state" => "nok"],
            ["id" => 7, "name" => "jfh", "state" => "ok"],
            ["id" => 8, "name" => "jfh", "state" => "ok"],
            ["id" => 9, "name" => "jfh", "state" => "ok"],
            ["id" => 10, "name" => "jfh", "state" => "ok"],
        ];
        return $this->render('admin/apis.html.twig', [
            'apis' => $apis,
        ]);
    }
}
