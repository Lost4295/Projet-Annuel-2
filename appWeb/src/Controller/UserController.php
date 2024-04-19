<?php

//src/Controller/UserController.php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class UserController extends AbstractController
{

    #[Route("/profile", name: "profile")]
    #[IsGranted("ROLE_USER")]
    public function profile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true,
                ],
                'constraints' => [
                    new Email([
                        'message' => 'The email {{ value }} is not a valid email.',
                    ]),
                    new NotBlank([
                        'message' => 'Please enter an email.',
                    ]),
                ],
                "label" => 'emailfield'

            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true,
                ],
                "label" => 'nom',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a name.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Your name should be at least {{ limit }} characters.',
                        // max length allowed by Symfony for security reasons
                        'max' => 80,
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true,
                ],
                "label" => 'prenom',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a name.',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Your name should be at least {{ limit }} characters.',
                        // max length allowed by Symfony for security reasons
                        'max' => 80,
                    ]),
                ],
                // 
            ])
            ->add('birthdate', DateType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true,
                ],
                "label" => 'birthdate',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a birthdate.',
                    ]),
                    new Type([
                        'message' => 'Please enter a valid date.',
                        'type' => 'DateTime',
                    ])
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true,
                ],
                "label" => 'phone',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a phone number.',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Your phone number should be at least {{ limit }} characters.',
                        // max length allowed by Symfony for security reasons
                        'max' => 10,
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]{10}$/',
                        'message' => 'Your phone number should contain only numbers.'
                    ])
                ],
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
    
    #[Route("/create_appart", name: "create_appart")]
    public function createAppart(Request $request, EntityManagerInterface $em)
    {
        return $this->render('create_appart.html.twig');
    }
}
