<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Email;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AppartementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'attr' => [
                    "class" => "my-2"
                ], "label" => "description"
            ])
            ->add('shortDesc', TextType::class, [
                'attr' => [
                    "class" => "my-2"
                ], "label" => "shortDesc"
            ])
            ->add('price', NumberType::class, [
                'attr' => [
                    "class" => "my-2"
                ], "label" => "price"
            ])
            ->add('address', TextType::class, [
                'attr' => [
                    "class" => "my-2",
                    'autocomplete'=> "street-address"
                ], "label" => "address"
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    "class" => "my-2",
                    'autocomplete'=> "address-level2"
                ], "label" => "city"
            ])
            ->add('postalCode', TextType::class, [
                'attr' => [
                    "class" => "my-2",
                    'autocomplete'=> "postal-code"
                ], "label" => "postalCode"
            ])
            ->add('country', TextType::class, [
                'attr' => [
                    "class" => "my-2",
                    'autocomplete'=> "country-name"
                ], "label" => "country"
            ])
            ->add('nbRooms', NumberType::class, [
                'attr' => [
                    "class" => "my-2"
                ], "label" => "nbRooms"
            ])
            ->add('note', NumberType::class, [
                'attr' => [
                    "class" => "my-2"
                ], "label" => "note"
            ])
            ->add('state', ChoiceType::class, [
                'attr' => [
                    "class" => "my-2"
                ], "label" => "state"
            ])
            // ->add('bailleur', EntityType::class, [
            //     'attr' => [
            //         "class" => "my-2"
            //     ], "label" => "bailleur"
            // ])
            ->add("submit", SubmitType::class, [
                "attr" => [
                    "class" => "btn btn-primary my-2"
                ], "label" => "submit"
            ])
            ;
    }
}
