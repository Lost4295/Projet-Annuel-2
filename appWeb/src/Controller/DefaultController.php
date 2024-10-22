<?php

//src/Controller/DefaultController.php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\Appartement;
use App\Entity\Fichier;
use App\Entity\Location;
use App\Entity\Service;
use App\Entity\User;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefaultController extends AbstractController
{


    #[Route("/", name: "index")]
    #[Route("/", name: "homepage")]
    public function index(Request $request, EntityManagerInterface $em, PdfService $pdf)
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
                $loca->setDateha(new \DateTime());
                $loca->setLocataire($em->getRepository(User::class)->findOneBy(["email"=>$info->locataire]));
                $finalPrice = 0;
                foreach ($info->service as $service) {
                    $s = $em->getRepository(Service::class)->find($service);
                    $loca->addService($s);
                    $finalPrice += $s->getTarifs();
                }
                $loca->setPrice($info->price+$finalPrice);
                $factu = new Fichier();
                $factu->setNom("Facture du " . date("d/m/Y"));
                $factu->setUser($user);
                $factu->setType("location");
                $path = $pdf->generatePdf($loca);
                if (file_exists($path[0])) {
                    $factu->setSize(PdfService::human_filesize(filesize($path[0])));
                    $factu->setPath($path[1]);
                    $em->persist($factu);
                } else {
                    $this->addFlash('danger', "errgeneratinginvoice");
                    
                }
                $em->persist($loca);
                $em->flush();
                $this->addFlash('success', "locationsuccess");
                $request->getSession()->remove('location');
                $request->getSession()->remove('uid');
            }
        }
        $paid = $request->query->get('a', null);
        $uid = null;
        if ($paid) {
            $uid = $request->getSession()->get('uid')->toRfc4122();
            if ($uid === $paid) {
                $id = $request->getSession()->get('aboid');
                $abo = $em->getRepository(Abonnement::class)->find($id);
                $user->setAbonnement($abo);
                $em->flush();
                $this->addFlash('success', "abosuccess");
                $request->getSession()->remove('uid');
                $request->getSession()->remove('aboid');
            }
        }
        $apparts = $em->getRepository(Appartement::class)->findBy([], ['note' => 'DESC']);
        $apparts = array_slice($apparts, 0, 9);
        return $this->render('index.html.twig', ['apparts' => $apparts]);
    }



    #[Route("/ventes", name: "ventes")]
    #[Route("/privacy", name: "privacy")]
    #[Route("/terms", name: "terms")]
    #[Route("/legal", name: "legalmentions")]
    #[Route("/about", name: "about")]
    public function legals(Request $request)
    {
        $name = $request->attributes->get("_route");
        $route = "legal/$name.html.twig";
        return $this->render($route);
    }

    #[Route("/faq", name: "faq")]
    #[Route("/contact", name: "contact")]
    public function contact(Request $request)
    {
        $name = $request->attributes->get("_route");
        $route = "info/$name.html.twig";
        return $this->render($route);
    }
    
}
