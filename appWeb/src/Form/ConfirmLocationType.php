<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfirmLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("firstForm", LocationFirstType::class)
            ->add("price", TextType::class, [
                "label" => false,
                "required" => true
            ])
            ->add("services", EntityType::class, [
                'class' => Service::class,
                'choice_label' => function (Service $ser): string {
                    return $ser->getTitre() . ' ( ' . $ser->getTarifs() . ' € )';
                },
                'multiple' => true,
                'expanded' => false,
                'label' => false,
                "attr" => [
                    "class" => "selectpicker",
                    "data-container"=>"body",
                    "data-live-search" => "true",
                    "data-dropup-auto" => "false",
                ],
                "required" => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
