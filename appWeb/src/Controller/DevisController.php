<?php

//src/Controller/DevisController.php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Fichier;
use App\EventSubscriber\ModerationSubscriber;
use App\Form\DevisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DevisController extends AbstractController
{

    #[Route("/devis", name: "devis")]
    public function table(Request $request, EntityManagerInterface $em)
    {
        $devis= $em->getRepository(Fichier::class)->findBy(["type" => "devis"]);

        return $this->render('devis.html.twig', [
            'devis' => $devis,
        ]);
    }

    
    #[Route("/devis/create", name: "create_devis")]
    public function créer(Request $request, EntityManagerInterface $em)
    {

        
        $form = $this->createForm(DevisType::class, null);
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder("modify_profile", DevisType::class, null, array(
            'auto_initialize'=>false // it's important!!!
        ));
        $builder->addEventSubscriber(new ModerationSubscriber($em, $request));
        $form = $builder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //... Ton code pour enregistrer le devis jsp ce que tu fais aporès
        }
        return $this->render('devis.html.twig', [
            'form' => $form,
        ]);
    }
}