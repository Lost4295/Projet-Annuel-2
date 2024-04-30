<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Appartement;
use App\Entity\AppartPlus;
use App\Form\AppartementType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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

    #[Route("/create_plusie", name: "create_plus")]
    public function createPlus(Request $request, EntityManagerInterface $em, AdminUrlGenerator $adminUrlGenerator)
    {
        $pluses = array_flip(AppartPlus::getAllPluses());
        $icons = AppartPlus::getIcons();


        // decode our icons
        $form = $this->createFormBuilder(null)
            ->add('icon', ChoiceType::class, [
                "required" => false,
                "choices" => $pluses,
                "choice_attr" => $icons,
                "attr" => [
                    "class" => "selectpicker"
                ]
            ])->add("appartement", EntityType::class, [
                "class" => Appartement::class,
                "choice_label" => "shortDesc",
                "attr" => [
                    "class" => "selectpicker"
                ]
            ])
            ->add("submit", SubmitType::class, [
                "label" => "Add",
                "attr" => [
                    "class" => "btn btn-primary"
                ],
            ])
            ->getForm()->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $appartement = $form->getData();
            $plus = new AppartPlus();
            $plus->setIcon($appartement['icon']);
            $plus->addAppartement($appartement['appartement']);
            $em->persist($plus);
            $em->flush();
            $url = $adminUrlGenerator
            ->setController(AppartementCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl();
            return $this->redirect($url);
        }
        return $this->render('admin/createplus.html.twig', [
            'form' => $form,
        ]);
    }
}
