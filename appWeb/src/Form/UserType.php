<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("role", ChoiceType::class, [
                'required' => true,
                'label' => 'register_choose',
                'expanded' => true,
                'empty_data' => "t",
                'choices' => [
                    'traveler' => "t",
                    'bailleur' => "b",
                    'presta' => "p",
                ],
            ])
            ->add("type", ChoiceType::class, [
                'required' => true,
                'label' => 'register_moral',
                'expanded' => true,
                'empty_data' => "p",
                'choices' => [
                    'physic' => "p",
                    'moral' => "m",
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control'
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
                    'class' => 'form-control'
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
                    'class' => 'form-control'
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
                    'class' => 'form-control'
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
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
                "label_html" => true,
                "label" => 'agree',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
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
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'type' => PasswordType::class,
                'options' => ['attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control'
                ],],
                "label" => 'password',
                'invalid_message' => 'invalidpwd',
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new PasswordStrength([
                        'minScore' => 1,
                        'message' => 'Your password is too easy to guess. Company\'s security policy requires to use a stronger password.'
                    ]),
                ],
            ])
            ->add('professionnel', ProfessionnelType::class, [
                'label' => 'pro',
                'attr' => [
                    'class' => 'form-control'
                ],
            ]);
    }
}
