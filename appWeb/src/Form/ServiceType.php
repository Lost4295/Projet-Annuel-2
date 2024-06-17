<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Professionnel;
use App\Entity\Service;
use PHPUnit\TextUI\XmlConfiguration\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'titre',
                'attr' => [
                    'class' => 'form-control mb-3 ms-2'
                ]
            ])
            ->add('description',TextareaType::class, [
                'label' => 'description',
                'attr'=> [
                    'rows' => 5,
                    'class' => 'form-control mb-3 ms-2'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'type',
                'choices' => [
                    'nettoyage' => Service::NETTOYAGE,
                    'electricite' => Service::ELECTRICITE,
                    'plomberie' => Service::PLOMBERIE,
                    'peinture' => Service::PEINTURE,
                    'bricolage' => Service::BRICOLAGE,
                    'chauffeur' => Service::CHAUFFEUR
                ],
                'attr' => [
                    'class' => 'form-control mb-3 ms-2'
                ]
            ])
            ->add('tarifs', NumberType::class, [
                'label' => 'tarifs',
                'attr' => [
                    'min' => 0,
                    'step' => 0.01,
                    'class' => 'form-control mb-3 ms-2'
                ]
            ])
            ->add('additionalInfo', TextareaType::class, [
                'label' => 'infossups',
                'attr' => [
                    'rows' => 5,
                    'class' => 'form-control mb-3 ms-2'
                ]
            ])
            ->add('images', FileType::class, [
                'label' => 'images',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-3 ms-2'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
