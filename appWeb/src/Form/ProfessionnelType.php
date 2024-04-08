<?php

namespace App\Form;

use App\Entity\Professionnel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Constraints\Regex;

class ProfessionnelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('societyName', TextType::class, [
                'label' =>  'societyname',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Your society name must be at least {{ limit }} characters long',
                        'max' => 255,
                        'maxMessage' => 'Your society name cannot be longer than {{ limit }} characters',
                    ]),
                    new NotBlank([
                        'message' => 'Please enter a society name',
                    ]),
                ],
            ])
            ->add('siretNumber', TextType::class, [
                'label' => 'siretnumber',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Length([
                        'min' => 14,
                        'minMessage' => 'Your SIRET number must be at least {{ limit }} characters long',
                        'max' => 14,
                        'maxMessage' => 'Your SIRET number cannot be longer than {{ limit }} characters',
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]{14}$/',
                        'message' => 'Your SIRET number must be 14 digits long',
                    ]),
                    new NotBlank([
                        'message' => 'Please enter a SIRET number',
                    ]),
                ],
            ])
            ->add('societyAddress', TextType::class, [
                'label' => 'societyaddress',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Your society address must be at least {{ limit }} characters long',
                        'max' => 255,
                        'maxMessage' => 'Your society address cannot be longer than {{ limit }} characters',
                    ]),
                    new NotBlank([
                        'message' => 'Please enter a society address',
                    ]),
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'city',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Your city must be at least {{ limit }} characters long',
                        'max' => 255,
                        'maxMessage' => 'Your city cannot be longer than {{ limit }} characters',
                    ]),
                    new NotBlank([
                        'message' => 'Please enter a city',
                    ]),
                ],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'postalcode',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Your postal code must be at least {{ limit }} characters long',
                        'max' => 5,
                        'maxMessage' => 'Your postal code cannot be longer than {{ limit }} characters',
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]{5}$/',
                        'message' => 'Your postal code must be 5 digits long',
                    ]),
                    new NotBlank([
                        'message' => 'Please enter a postal code',
                    ]),
                ],
            ])
            ->add('country', TextType::class, [
                'label' => 'country',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Your country must be at least {{ limit }} characters long',
                        'max' => 255,
                        'maxMessage' => 'Your country cannot be longer than {{ limit }} characters',
                    ]),
                    new NotBlank([
                        'message' => 'Please enter a country',
                    ]),
                ],
            ]);
        // ->add('responsable', User::class, [
        //     'label'=> 'responsable',
        //      'attr'=> [
        //         'class' => 'form-control'
        //     ],
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Professionnel::class,
        ]);
    }
}
