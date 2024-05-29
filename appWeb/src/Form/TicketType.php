<?php

namespace App\Form;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('titre', TextType::class, [
            'attr'=>[
                "class"=> "form-control"
            ]
        ])
        ->add('dateOuverture', DateTimeType::class, [
                'attr'=>[
                    "class"=> "form-control",
                    "readonly"=> "readonly"
                ]
            ])
            ->add('category', ChoiceType::class, [
                'attr'=>[
                    "class"=> "form-control"
                ],
                'choices'=>array_flip(Ticket::CATEGORY_LIST)
            ])
            ->add('type', ChoiceType::class, [
                'attr'=>[
                    "class"=> "form-control"
                ],
                'choices'=>array_flip(Ticket::TYPE_LIST)
            ])
            ->add('priority', ChoiceType::class, [
                'attr'=>[
                    "class"=> "form-control"
                ],
                'choices'=>array_flip(Ticket::PRIORITY_LIST)
            ])
            ->add('urgence', ChoiceType::class, [
                'attr'=>[
                    "class"=> "form-control"
                ],
                'choices'=>array_flip(Ticket::URGENCE_LIST)
            ])
            ->add('description', TextareaType::class, [
                'attr'=>[
                    "class"=> "form-control"
                ]
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
