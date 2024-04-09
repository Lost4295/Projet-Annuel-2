<?php
// src/Controller/Admin/AbonnementController.php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AbonnementController extends AbstractController
{
    #[Route("/tarificationfds",name: "tarifs")]
    public function Tarifs(Request $request): Response
    {
        $abos = [
            ["nom"=>"abonnement1","prix"=>"10€","description"=>"description1"],
            ["nom"=>"abonnement2","prix"=>"20€","description"=>"description2"],
            ["nom"=>"abonnement3","prix"=>"30€","description"=>"description3"],
            ["nom"=>"abonnement4","prix"=>"40€","description"=>"description4"],
            ["nom"=>"abonnement5","prix"=>"50€","description"=>"description5"],
        ];
        $transform = [];
        $transform["key"] = [null];
        $transform["nom"] = [null];
        $transform["prix"] = [null];
        $transform["description"] = [null];
        $transform["duree"] = [null];
        foreach ($abos as $key => $abo) {
            $transform["key"][] = $key;
            $transform["nom"][] =  $abo["nom"];
            $transform["prix"][] =  $abo["prix"];
            $transform["description"][] =  $abo["description"];
            $transform["duree"][] = boolval(rand(0,1));
        }
        dump($transform, $abos);
        return $this->render('admin/tarifs.html.twig', [
            'abonnements'=> $transform,
        ]);
    }
    // ...
}
