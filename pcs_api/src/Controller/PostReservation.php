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
        '/create-reservation',
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
    public function tot(): Response
    {
        try {
            $jsonStr = file_get_contents('php://input');
            $jsonObj = json_decode($jsonStr);
            $loc = new Location();
            $loc->setDateha(new \DateTime($jsonObj->dateha));
            $loc->setDateDebut(new \DateTime($jsonObj->dateDebut));
            $loc->setDateFin(new \DateTime($jsonObj->dateFin));
            $loc->setPrice($jsonObj->price);
            $loc->setLocataire($this->em->getRepository(User::class)->find($jsonObj->user));
            $loc->setAppartement($this->em->getRepository(Appartement::class)->find($jsonObj->appartement));
            $loc->setAdults($jsonObj->adults);
            $loc->setKids($jsonObj->kids);
            $loc->setBabies($jsonObj->babies);
            foreach ($jsonObj->services as $service) {
                $loc->addService($this->em->getRepository(Service::class)->find($service));
            }
            $this->em->persist($loc);
            $this->em->flush();
            $file = new Fichier();
            $file->setLocation($loc);
            $file->setDate(new \DateTime());
            $file->setUser($loc->getLocataire());
            $file->setNom("Facture de location du " . $file->getDate()->format('d/m/Y'));
            $file->setType('location');
            $fac = $this->pdfService->generatePdf($loc);
            if (file_exists($fac[0])) {
                $file->setSize(PdfService::human_filesize(filesize($fac[0])));
                $file->setPath($fac[1]);
            }
        } catch (\Error $e) {
            return $this->json(["error" => "Invalid JSON"]);
        }
        return $this->json(["data" => "Reservation created"]);
    }
}
