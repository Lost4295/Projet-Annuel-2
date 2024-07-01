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
                "Janvier" => "january",
                "Février" => "february",
                "Mars" => "march",
                "Avril" => "april",
                "Mai" => "may",
                "Juin" => "june",
                "Juillet" => "july",
                "Août" => "august",
                "Septembre" => "september",
                "Octobre" => "october",
                "Novembre" => "november",
                "Décembre" => "december",
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
