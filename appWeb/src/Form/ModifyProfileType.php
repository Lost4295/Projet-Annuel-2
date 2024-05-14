<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class ModifyProfileType extends AbstractType
{
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
