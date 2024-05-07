<?php
// src/Controller/Admin/AbonnementController.php
namespace App\Controller\Admin;

use App\Entity\Abonnement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AbonnementController extends AbstractController
{
    #[Route("/tarificationfds",name: "tarifs")]
    public function Tarifs(EntityManagerInterface $em): Response
    {
        $abos = $em->getRepository(Abonnement::class)->findAll();
        $transform = [];
        $options = [];
        $transform["key"] = [null];
        $transform["nom"] = [null];
        $transform["prix"] = [null];
        $transform["duree"] = [null];
        foreach ($abos as $key => $abo) {
            $transform["key"][] = $key;
            $transform["prix"][] =  $abo->getTarif();
            $transform["nom"][] =  $abo->getNom();
            foreach ($abo->getOptions() as $option) {
                $options[$option->getOption()->getNom()][] = ($option->isPresence())? "1" : "0";
            }

            // $transform["description"][] =  $abo["description"];
            $transform["duree"][] = boolval(rand(0,1));
        }
        dump($transform, $abos, $options);
        return $this->render('admin/tarifs.html.twig', [
            'abonnements'=> $transform,
            'options' => $options
        ]);
    }
    // ...
}
