<?php

//src/Controller/UserController.php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\Professionnel;
use App\Entity\User;
use App\Form\AppartementType;
use App\Form\TicketType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
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

        $appartements = $locations = $pastlocas = [];
        if ($user->hasRole(User::ROLE_BAILLEUR)) {
            $pro = $em->getRepository(Professionnel::class)->findOneBy(["responsable" => $user->getId()]);
            $appartements = $pro->getAppartements();
        }
        
        $reservation = $this->createForm(AppartementType::class);
        $reservation->handleRequest($request);
        if ($reservation->isSubmitted() && $reservation->isValid()) {
            $appartement = $reservation->getData();
            dd($appartement);
            $em->persist($appartement);
            $em->flush();
            return $this->redirectToRoute('appartement_create');
        }
        if ($user->hasRole(User::ROLE_VOYAGEUR)) {
            $locations = $em->getRepository(Location::class)->findBy(["locataire" => $user->getId()]);
            foreach ($locations as $key => $location) {
                if ($location->getDateDebut() < new \DateTime('now') && $location->getDateFin() < new \DateTime('now')) {
                    $pastlocas[] = $location;
                    unset($locations[$key]);
                }
            }
        }

        // $form = $this->createForm(UserType::class, $user)->handleRequest($request);
        return $this->render('user/profile.html.twig', [
            'message' => 'Hello World!',
            'user' => $user,
            'appartements' => $appartements,
            'locations' => $locations,
            'pastlocations' => $pastlocas,
            'reservation' => $reservation
        ]);
    }

    #[Route("/create_appart/f", name: "create_appart")]
    public function createAppart(Request $request, EntityManagerInterface $em)
    {
        return $this->render('create_appart.html.twig');
    }

    #[Route("profile/modify/", name: "check_infos")]
    #[IsGranted("ROLE_USER")]

    public function checkInfos(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
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
                    'disabled' => 'disabled',
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
                    'disabled' => 'disabled',
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
                    'disabled' => 'disabled',
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
            $user->setIsVerified(false);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Your profile has been updated successfully. However, you need to verify your email address.');
            return $this->redirectToRoute('profile');
        }

        return $this->render('user/modprofile.html.twig', ['user' => $form]);
    }

    #[Route("/ticket", name: "create_ticket")]
    #[IsGranted("ROLE_USER")]
    public function createTicket(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(TicketType::class, null);
        return $this->render('user/create_ticket.html.twig', ['form' => $form]);
    }
}
