<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Email;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EmailType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('destinataire', TextType::class, [
                'attr' => [
                    'class' => 'form-control my-1'
                ],
                'label' => 'destinataire'
            ])
            ->add('cc', TextType::class, [
                'attr' => [
                    'class' => 'form-control my-1'
                ],
                'label' => 'cc',
                'required' => false
            ])
            ->add('object', TextType::class, [
                'attr' => [
                    'class' => 'form-control my-1'
                ],
                'label' => 'object'
                
            ])
            ->add('isAutomatic', CheckboxType::class, [
                'attr' => [
                    'class' => 'form-check-input mx-2'
                ],
                'label' => 'auto'
            ])
            ->add('date', DateTimeType::class, [
                'attr' => [
                    'class' => 'form-control my-1'
                ],
                'label' => 'date'
            ])
            ->add('body', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control my-1'
                ],
                'label' => 'body'
            ])
            ->add("pj", FileType::class, [
                'attr' => [
                    'class' => 'form-control my-1'
                ],
                'multiple'=>true,
                'label' => 'pj',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Email::class,
        ]);
    }
}
