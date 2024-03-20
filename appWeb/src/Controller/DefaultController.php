<?php

//src/Controller/DefaultController.php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{


    #[Route("/index", name: "index")]
    #[Route("/", name: "homepage")]
    public function index()
    {
        return $this->render('index.html.twig', ['message' => 'Hello World!']);
    }

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



    #[Route("/service", name: "services")]
    public function table()
    {
        return $this->render('service.html.twig', [
        ]);
    }

    #[Route("/cookies", name: "cookies")]
    #[Route("/ventes", name: "ventes")]
    #[Route("/privacy", name: "privacy")]
    #[Route("/terms", name: "terms")]
    #[Route("/about", name: "about")]
    public function legals( Request $request)
    {
        $name = $request->attributes->get("_route");
        $route = "legal/" . $name . ".html.twig";
        return $this->render($route);
    }

    #[Route("/faq", name: "faq")]
    #[Route("/contact", name: "contact")]
    #[Route("/pricing", name: "pricing")]
    public function contact(Request $request)
    {
        $name = $request->attributes->get("_route");
        $route = "info/" . $name . ".html.twig";
        return $this->render($route);
    }

}
