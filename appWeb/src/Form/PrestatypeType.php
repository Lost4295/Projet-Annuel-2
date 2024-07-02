<?php

namespace App\Form;

use App\Entity\Professionnel;
use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestatypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('type', ChoiceType::class, [
            'attr'=>[
                'class'=>'selectpicker'
            ],
            'choices'  => array_flip(Service::getTypes()),
            'multiple' => false,
            'expanded' => false,
            'help_attr'=> ["class"=>"text-danger"],
            'help'=>"Attention, vous ne pourrez pas modifier cette valeur plus tard !"
        ])
        ->add('submit', SubmitType::class, [
            'attr'=>[
                'class'=>'btn btn-primary my-1'
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
