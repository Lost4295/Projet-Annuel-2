<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Appartement;
use App\Form\AppartementType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppartementController extends AbstractController
{
    
        #[Route("/create_appart", name: "appartement_create")]
    
        public function index(Request $request, EntityManagerInterface $em): Response
        {

            $form = $this->createForm(AppartementType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $appartement = $form->getData();
                dd($appartement);
                $em->persist($appartement);
                $em->flush();
                return $this->redirectToRoute('appartement_create');
            }
            return $this->render('admin/createappart.html.twig', [
                'form' => $form,
            ]);
        }
    
}