<?php

//src/Controller/UserController.php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{

    #[Route("/profile", name: "profile")]
    #[IsGranted("ROLE_USER")]
    public function profile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('nom', null, [
                "attr"=>[
                    "class"=>"form-control",
                    "disabled"=>"disabled"
                ]
            ])
            ->add('prenom', null, [
                "attr"=>[
                    "class"=>"form-control",
                    "disabled"=>"disabled"
                ]
            ])
            ->add('birthdate', null, [
                "attr"=>[
                    "class"=>"form-control",
                    "disabled"=>"disabled"
                ]
            ])
            ->add('phoneNumber', null, [
                "attr"=>[
                    "class"=>"form-control",
                    "disabled"=>"disabled"
                ]
            ])
            ->add('email', null, [
                "attr"=>[
                    "class"=>"form-control",
                    "disabled"=>"disabled"
                ]
            ])
            ->getForm()->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('profile');
        }
        // $form = $this->createForm(UserType::class, $user)->handleRequest($request);
        return $this->render('profile.html.twig', ['message' => 'Hello World!', 'user' => $form]);
    }
}
