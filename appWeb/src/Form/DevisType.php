<?php

namespace App\Form;

use App\Entity\Devis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

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
                    'class' => 'form-control my-1'
                ],
                'label' => 'Email'
            ])
            ->add('prestation', ChoiceType::class, [
                'choices'  => [
                    'Nettoyage' => Devis::PRESTA_NETTOYAGE,
                    'Electricite' => Devis::PRESTA_ELEC,
                    'Plomberie' => Devis::PRESTA_PLOMBERIE,
                    'Peinture' => Devis::PRESTA_PEINTURE,
                    'Bricolage' => Devis::PRESTA_BRICOLAGE
                ],
                'label' => 'Type de prestation',
            ])
            ->add('contact', ChoiceType::class, [
                'choices'  => [
                    'Email' => 0,
                    'Telephone' => 1,

                ],
                'label' => 'Comment souhaitez être contacté ?',
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control my-1'
                ],
                'label' => 'Description'
            ]);
    }
}
