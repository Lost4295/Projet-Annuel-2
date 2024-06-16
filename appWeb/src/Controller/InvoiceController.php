<?php
// src/Controller/InvoiceController.php
namespace App\Controller;

use App\Service\PdfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    
    #[Route("/invoice/generate", name: "generate_invoice")]
    public function generateInvoice(PdfService $pdfService): Response
    {
        // Assume the user is logged in and you can get the user details
        $user = $this->getUser();

        // Replace this with your actual data
        $data = [
            'user' => $user,
            'appart' => [
                'city' => 'Paris',
                'shortDesc' => 'Appartement cosy au cœur de la ville',
                'price' => 100,
                'images' => ['image1.jpg', 'image2.jpg']
            ],
            'dates' => [new \DateTime(), (new \DateTime())->modify('+5 days')],
            'days' => 5,
            'p1' => 500,
            'p2' => 25
        ];

        return $pdfService->generatePdf($data);
    }
}
