<?php
// src/Controller/TotoController.php
namespace App\Controller;

use App\Entity\Toto;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TotoController extends AbstractController
{
    #[Route('/tata/number')]
    public function number(): Response
    {
        $number = random_int(0, 100);
        $toto = new Toto();
        $toto->setTiti($number);
        return $this->render('lucky/number.html.twig', [
            'number' => $number,
        ]);
    }
}
