<?php

namespace App\Form;

use App\Entity\Appartement;
use App\Entity\Location;
use App\Entity\Service;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationFirstType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('date', TextType::class, [
            "attr" => [
                "class" => "form-control",
                "id" => "startdate",
            ],
        ])->add('adults',NumberType::class, [
            "attr"=>[
                "class"=>"form-control",
                "min"=>0,
                "max"=>6
            ],
            'label'=>"adults"
        ])
            ->add('kids',NumberType::class, [
                "attr"=>[
                    "class"=>"form-control",
                    "min"=>0,
                    "max"=>6
                ],
                'label'=>"kids"
            ])
            ->add('babies',NumberType::class, [
                "attr"=>[
                    "class"=>"form-control",
                    "min"=>0,
                    "max"=>6
                ],
                'label'=>"babies"
            ]);
    }
}