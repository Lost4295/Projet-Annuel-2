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

        $connected = $request->query->get('e', null);
        if ($connected) {
            $this->addFlash('danger', "unauthorized");
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
                $loca->setPrice($info->price);
                foreach ($info->service as $service) {
                    $s = $em->getRepository(Service::class)->find($service);
                    $loca->addService($s);
                }
                $factu = new Fichier();
                $factu->setNom("Facture du " . date("d/m/Y"));
                $factu->setUser($user);
                $factu->setType("pdf");
                $path = $pdf->generatePdf($loca);
                if (file_exists($path[0])) {
                    $factu->setSize(PdfService::human_filesize(filesize($path[0])));
                    $factu->setPath($path[1]);
                    $em->persist($factu);
                    
                    
                } else {
                    $this->addFlash('danger', "Échec de la génération de la facture.");
                    
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
    #[Route('/invoice/generate-monthly', name: 'generate_monthly_invoice')]
    #[IsGranted("ROLE_USER")]
    public function generateMonthlyInvoice(EntityManagerInterface $em, PdfService $pdf, Request $request)
    {
        dd($request);
        $user = $this->getUser();
        $invoices = $em->getRepository(Location::class)->findMonthlyInvoicesByUser($user, new \DateTime('first day of this month'), new \DateTime('last day of this month'));

        if (empty($invoices)) {
            $this->addFlash('warning', 'No invoices found for this month.');
            return $this->redirectToRoute('profile');
        }

        $path = $pdf->generateMonthlyPdf($invoices, $user);

        // Redirect or display the PDF
        return $this->file($path);
    }
}
