<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class ModifyProfileType extends AbstractType
{
    public function __construct(
        private Security $security,
    ) {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                    'readonly' => 'readonly',
                ],
                "label" => 'nom',
                'required'=>false,
                
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
                    'readonly' => 'readonly',
                ],
                "label" => 'prenom',
                'required'=>false,
                
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
                    'readonly' => 'readonly',
                ],
                "label" => 'birthdate',
                'required'=>false,
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
            // 
            ;
        $user = $this->security->getUser();
        if (!$user) {
            throw new \LogicException(
                'The ModifyProfileType cannot be used without an authenticated user!'
            );
        }
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($user): void {
                $form = $event->getForm();
                if ($user->hasRole(User::ROLE_BAILLEUR) || $user->hasRole(User::ROLE_PRESTA)) {
                    $form->add('image', FileType::class, [
                        "mapped" => false,
                        "label" => "image",
                        'attr' => [
                            'class' => 'form-control',
                        ],
                        'constraints' => [
                            new Image([
                                'maxSize' => '5M',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                    'image/gif',
                                ],
                                'mimeTypesMessage' => 'Please upload a valid image',
                                'minWidth' => 20,
                                'minWidthMessage' => 'The image width is too small ({{ width }}px). Minimum width should be {{ min_width }}px.',
                                'minHeight' => 20,
                                'minHeightMessage' => 'The image height is too small ({{ height }}px). Minimum height should be {{ min_height }}px.',
                            ]),
                        ]
                    ])
                    ->add("pro", ProfessionnelType::class, [
                        'label' => 'pro',
                        "mapped" => false,
                        'attr' => [
                            'class' => 'form-control',
                        ],
                    ]);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
