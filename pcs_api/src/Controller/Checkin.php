<?php

namespace App\Controller;

use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

#[AsController]
class Checkin extends AbstractController
{


    #[Route(
        name: 'checkin',
        path: 'checkin',
        methods: ['POST'],
        defaults: ['_api_resource_class' => 'App\Entity\Location', '_api_item_operation_name' => 'checkin'],
    )]
    public function checkin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
            exit();
        }
        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid Content Type']);
            exit();
        }
        header('Content-Type: application/json');

        try {
            $jsonStr = file_get_contents('php://input');
            $jsonObj = json_decode($jsonStr);
            $user = $this->getUser();
            $locs = $user->getLocations();
            foreach ($locs as $loc) {
                $pairs = [];
                $pairs[] = [$loc->getAppartement()->getId(), $jsonObj->id];
                if ($loc->getAppartement()->getId() == $jsonObj->id) {
                    return $this->json(["data" => "OK"]);
                }
            }
            return $this->json(["data" => "NOK", "checked"=> $pairs]);
        } catch (\Error $e) {
            return $this->json(["data" => "Invalid JSON"]);
        }
    }
}
