<?php
// src/Service/PdfService.php
namespace App\Service;

use App\Entity\Location;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class PdfService
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function generatePdf(Location $location): string
    {
        // Configure DomPDF according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->twig->render('invoice.html.twig', [
            'location' => $location
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Generate a unique filename and save the PDF
        $filename = 'invoice_' . uniqid() . '.pdf';
        $outputPath = __DIR__ . '/../../public/files/pdfs' . $filename;
        file_put_contents($outputPath, $dompdf->output());

        // Return the path to the saved PDF
        return $filename;
    }
    public static function human_filesize($bytes, $decimals = 2) {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}