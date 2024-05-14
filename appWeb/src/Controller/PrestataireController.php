<?php

//src/Controller/PrestataireController.php

namespace App\Controller;

use App\Entity\Professionnel;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route("/prestataires")]
class PrestataireController extends AbstractController
{
    #[Route(name: "prestataires")]
    public function prestataires(Request $request, EntityManagerInterface $em)
    {

        
        $prestas = $em->getRepository(Professionnel::class)->findByRole(User::ROLE_PRESTA);
        return $this->render('presta/index.html.twig', [
            'prestas' => $prestas,

        ]);
    }
    #[Route("/detail", name: "detail_presta")]
    public function show(Request $request, EntityManagerInterface $em)
    {
        //TODO faire la page de detail
        return $this->render('presta/presting.html.twig');
    }

}