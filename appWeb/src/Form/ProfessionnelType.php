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
            ])
            ->add('siretNumber', TextType::class, [
                'label' => 'siretnumber',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('societyAddress', TextType::class, [
                'label' => 'societyaddress',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'city',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'postalcode',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('country', TextType::class, [
                'label' => 'country',
                'attr' => [
                    'class' => 'form-control'
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
