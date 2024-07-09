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
                'label' => 'prenom'
            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'form-control my-1'
                ],
                'label' => 'nom'
            ])
            ->add('numero', TextType::class, [
                'attr' => [
                    'class' => 'form-control my-1'
                ],
                'label' => 'phone'

            ])
            ->add('email', TextType::class, [
                'attr' => [
                    'class' => 'form-control my-1'
                ],
                'label' => 'email'
            ])
            ->add('prestation', ChoiceType::class, [
                'choices'  => [
                    'Nettoyage' => Devis::PRESTA_NETTOYAGE,
                    'Electricite' => Devis::PRESTA_ELEC,
                    'Plomberie' => Devis::PRESTA_PLOMBERIE,
                    'Peinture' => Devis::PRESTA_PEINTURE,
                    'Bricolage' => Devis::PRESTA_BRICOLAGE
                ],
                'label' => 'prestype',
            ])
            ->add('contact', ChoiceType::class, [
                'choices'  => [
                    'email' => 0,
                    'telephone' => 1,

                ],
                'label' => 'contact',
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control my-1'
                ],
                'label' => 'description'
            ]);
    }
}
