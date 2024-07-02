<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisFinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("estimatedTime", DateIntervalType::class, [
                'attr' => [
                    'class' => 'form-control my-1'
                ],
                'input' => 'string',
                'with_years'  => false,
                'with_months' => false,
                'with_days'   => true,
                'with_hours'  => true,
                'with_minutes' => true,
                'labels' => [
                    'invert' => 'invert',
                    'years' => 'years',
                    'months' => 'months',
                    'weeks' => 'weeks',
                    'days' => 'days',
                    'hours' => 'hours',
                    'minutes' => 'minutes',
                    'seconds' => 'seconds',
                ]
            ])
            ->add("startDate", DateTimeType::class, ['attr' => [
                'class' => 'form-control my-1'
            ],])
            ->add("endDate", DateTimeType::class, ['attr' => [
                'class' => 'form-control my-1'
            ],])
            ->add("prix", NumberType::class, ['attr' => [
                'class' => 'form-control my-1',
                'step' => 0.01,
            ],]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
