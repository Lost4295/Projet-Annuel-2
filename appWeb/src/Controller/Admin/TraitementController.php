<?php
// src/Controller/Admin/TraitementController.php
namespace App\Controller\Admin;

use App\Entity\Professionnel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;


class TraitementController extends AbstractController
{
    private $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    #[Route("/states", name: "all_states")]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $unverifiedPros = $em->getRepository(Professionnel::class)->findBy(["isVerified" => false]);
        $urls = [];
        foreach ($unverifiedPros as $pro) {
            if ($pro->getJustification() == null) {
                continue;
            }
            $id = $pro->getId();
            $urls[$pro->getId()]["id"] = $id;
            $urls[$pro->getId()]["name"] = $pro->getSocietyName();
            $urls[$pro->getId()]["siret"] = $pro->getSiretNumber();
            $urls[$pro->getId()]["address"] = $pro->getSocietyAddress(). " " . $pro->getPostalCode() . " " . $pro->getCity() . " " . $pro->getCountry();
            $urls[$pro->getId()]["url"] = $this->adminUrlGenerator->setRoute('action_state',["id"=>$id])->generateUrl();
        }
        return $this->render('admin/traitements.html.twig', ['traitements'=>$urls]);
    }
    #[Route("/omh/{id}", name: "action_state")]
    public function specific(Request $request, $id, EntityManagerInterface $em): Response
    {
        $id = $request->get('id');
        $pro = $em->getRepository(Professionnel::class)->find($id);
        $val= $this->adminUrlGenerator->setRoute("val", ["id"=>$id])->generateUrl();
        $ref= $this->adminUrlGenerator->setRoute("ref", ["id"=>$id])->generateUrl();
        
        return $this->render('admin/traitement.html.twig', [
            "pro" => $pro,
            "val"=> $val,
            "ref"=> $ref
        ]);
    }

    #[Route("/val/{id}", name: "val")]
    public function val($id, Request $request, EntityManagerInterface $em){
        $ide = $request->get('id');
        $pro = $em->getRepository(Professionnel::class)->find($ide);
        $pro->setIsVerified(true)->setJustification(null);
        $em->persist($pro);
        $em->flush();
        $this->addFlash("success","valsucc");
        return $this->redirect($this->adminUrlGenerator->setRoute("all_states")->generateUrl());
    }

    #[Route("/ref/{id}", name: "ref")]
    public function ref($id, Request $request, EntityManagerInterface $em){
        $ide = $request->get('id');
        $pro = $em->getRepository(Professionnel::class)->find($ide);
        $pro->setIsVerified(false)->setJustification(null);
        $em->persist($pro);
        $em->flush();
        $this->addFlash("warning","suppsucc");
        return $this->redirect($this->adminUrlGenerator->setRoute("all_states")->generateUrl());
    }


    // ...
}
