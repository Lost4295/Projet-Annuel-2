<?php

namespace App\Controller;

use App\Entity\Appartement;
use App\Entity\Fichier;
use App\Entity\Location;
use App\Entity\Service;
use App\Entity\User;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;


class PostReservation extends AbstractController
{

    #[Route(
        '/loca',
        name: 'create_location',
        methods: ['GET', 'POST']
    )]

    private PdfService $pdfService;
    private EntityManagerInterface $em;
    public function __construct(PdfService $pdfService, EntityManagerInterface $em)
    {
        $this->pdfService = $pdfService;
        $this->em = $em;
    }
    public function loca(): Response
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
            $loc = new Location();
            $loc->setDateha(new \DateTime($jsonObj->dateha));
            $loc->setDateDebut(new \DateTime($jsonObj->dateDebut));
            $loc->setDateFin(new \DateTime($jsonObj->dateFin));
            $loc->setPrice($jsonObj->price);
            $loc->setLocataire($this->em->getRepository(User::class)->find($jsonObj->locataire));
            $loc->setAppartement($this->em->getRepository(Appartement::class)->find($jsonObj->appartement));
            $loc->setAdults($jsonObj->adults);
            $loc->setKids($jsonObj->kids);
            $loc->setBabies($jsonObj->babies);
            $loc->setFactToCreate(true);
            $this->em->persist($loc);
            $this->em->flush();
        } catch (\Error $e) {
            return $this->json(["error" => "Invalid JSON"]);
        }
        return $this->json(["data" => "Reservation created"]);
    }
}
