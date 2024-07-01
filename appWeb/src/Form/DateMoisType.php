<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateMoisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('mois', ChoiceType::class, [
            "attr" => [
                "class" => "form-control",
                "id" => "startdate",
            ],
            "choices" => [
                "Janvier" => 1,
                "Février" => 2,
                "Mars" => 3,
                "Avril" => 4,
                "Mai" => 5,
                "Juin" => 6,
                "Juillet" => 7,
                "Août" => 8,
                "Septembre" => 9,
                "Octobre" => 10,
                "Novembre" => 11,
                "Décembre" => 12,
            ],
            "expanded"=> false,
            "multiple"=> false,
            
        ])
        ->add('valider', SubmitType::class, [
            "attr" => [
                "class" => "btn btn-primary",
            ]
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
