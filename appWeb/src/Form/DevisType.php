<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Email;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DevisType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('prenom', TextType::class, [
            'attr' => [
                'class' => 'form-control my-1'
            ],
            'label' => 'Prénom'
        ])
        ->add('nom', TextType::class, [
            'attr' => [
                'class' => 'form-control my-1'
            ],
            'label' => 'Nom'
        ])
        ->add('numero', TextType::class, [
            'attr' => [
                'class' => 'form-control my-1'
            ],
            'label' => 'Numéro'
            
        ])
        ->add('email', TextType::class, [
            'attr' => [
                'class' => 'form-controle my-1'
            ],
            'label' => 'Email'
        ])
        ->add('prestation', ChoiceType::class, [
            'choices'  => [
                'Nettoyage' => 'Nettoyage',
                'Electricite' => 'Electricite',
                'Plomberie' => 'Plomberie',
                'Peinture' => 'Peinture',
                'Bricolage' => 'Bricolage'
            ],
            'label' => 'Type de prestation',
        ])
        ->add('contact', ChoiceType::class, [
            'choices'  => [
                'Email' => 'Email',
                'Telephone' => 'Telephone',
                
            ],
            'label' => 'Comment souhaitez être contacté ?',
        ])
        ->add('description', TextType::class, [
            'attr' => [
                'class' => 'form-controle my-1'
            ],
            'label' => 'Description'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Email::class,
        ]);
    }
}
