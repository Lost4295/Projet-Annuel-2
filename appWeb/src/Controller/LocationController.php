<?php

//src/Controller/LocationController.php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class LocationController extends AbstractController
{
    #[Route("/locations", name: "locations")]
    public function createPage(Request $request)
    {
        $routeName = $request->attributes->get("_route");

        if ($routeName === "number") {
            $number = random_int(0, 100);
        } else {
            $number = $routeName;
        }

        return $this->render('dashboard.html.twig', [
            'text' => $number,
        ]);
    }
}