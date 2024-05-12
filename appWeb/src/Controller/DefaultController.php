<?php

//src/Controller/DefaultController.php

namespace App\Controller;

use App\Entity\Appartement;
use App\Entity\Location;
use App\Entity\Professionnel;
use App\Entity\Service;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{


    #[Route("/", name: "index")]
    #[Route("/", name: "homepage")]
    public function index(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        if ($user && !$user->isVerified()) {
            $this->addFlash('warning', "plsverif");
        }
        $paid = $request->query->get('c', null);
        $uid = null;
        $loca = null;
        if ($paid) {
            $uid = $request->getSession()->get('uid')->toRfc4122();
            if ($uid === $paid) {
                $info = $request->getSession()->get('location');
                $loca = new Location();
                $loca->setAppartement($em->getRepository(Appartement::class)->find($info->appartement));
                $loca->setAdults($info->adults);
                $loca->setKids($info->kids);
                $loca->setBabies($info->babies);
                $loca->setDateDebut($info->dateDebut);
                $loca->setDateFin($info->dateFin);
                $loca->setLocataire($em->getRepository(User::class)->findOneBy(["email"=>$info->locataire]));
                $loca->setPrice($info->price);
                foreach ($info->service as $service) {
                    $s = $em->getRepository(Service::class)->find($service);
                    $loca->addService($s);
                }
                $em->persist($loca);
                $em->flush();
                $this->addFlash('success', "locationsuccess");
                $request->getSession()->remove('location');
                $request->getSession()->remove('uid');
            }
        }
        $apparts = $em->getRepository(Appartement::class)->findAll();
        return $this->render('index.html.twig', ['apparts' => $apparts]);
    }



    #[Route("/cookies", name: "cookies")]
    #[Route("/ventes", name: "ventes")]
    #[Route("/privacy", name: "privacy")]
    #[Route("/terms", name: "terms")]
    #[Route("/legal", name: "legalmentions")]
    #[Route("/about", name: "about")]
    public function legals(Request $request)
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
