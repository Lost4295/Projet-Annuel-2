<?php

//src/Controller/DefaultController.php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{


    #[Route("/index", name: "index")]
    #[Route("/", name: "homepage")]
    public function index(TranslatorInterface $translator)
    {
        $user = $this->getUser();
        if ($user && !$user->isVerified()) {
            $this->addFlash('warning', $translator->trans('verify'));
        }
        return $this->render('index.html.twig', ['message' => 'Hello World!']);
    }



    #[Route("/cookies", name: "cookies")]
    #[Route("/ventes", name: "ventes")]
    #[Route("/privacy", name: "privacy")]
    #[Route("/terms", name: "terms")]
    #[Route("/legal", name:"legalmentions")]
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
