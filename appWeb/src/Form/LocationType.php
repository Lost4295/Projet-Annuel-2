<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', TextType::class, [
                "attr" => [
                    "class" => "form-control",
                    "id" => "startdate",
                ],
            ])
            // ->add('dateFin', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('adults')
            ->add('kids')
            ->add('babies')
            // ->add('appartement', EntityType::class, [
            //     'class' => Appartement::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('locataire', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('services', EntityType::class, [
            //     'class' => Service::class,
            //     'choice_label' => 'id',
            // ])
            ;
    }


}
