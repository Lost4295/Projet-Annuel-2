<?php

namespace App\Form;

use App\Entity\Appartement;
use App\Entity\Location;
use App\Entity\Service;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationTestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', null, [
                'widget' => 'single_text',
            ])
            ->add('dateFin', null, [
                'widget' => 'single_text',
            ])
            ->add('adults')
            ->add('kids')
            ->add('babies')
            ->add('price')
            ->add('appartement', EntityType::class, [
                'class' => Appartement::class,
                'choice_label' => 'id',
            ])
            ->add('locataire', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('services', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
