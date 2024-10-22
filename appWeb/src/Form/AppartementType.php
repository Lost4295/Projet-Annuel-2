<?php

namespace App\Form;

use App\Entity\AppartPlus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\Professionnel;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;

class AppartementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'attr' => [
                    "class" => "my-2"
                ], "label" => "Titre"
            ])
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
                    'autocomplete' => "street-address"
                ], "label" => "address"
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    "class" => "my-2",
                    'autocomplete' => "address-level2"
                ], "label" => "city"
            ])
            ->add('postalCode', TextType::class, [
                'attr' => [
                    "class" => "my-2",
                    'autocomplete' => "postal-code"
                ], "label" => "postalCode"
            ])
            ->add('country', TextType::class, [
                'attr' => [
                    "class" => "my-2",
                    'autocomplete' => "country-name"
                ], "label" => "country"
            ])
            ->add('nbVoyageurs', NumberType::class, [
                'attr' => [
                    "class" => "my-2"
                ], "label" => "nbVoyageurs"
            ])
            ->add('note', NumberType::class, [
                'attr' => [
                    "class" => "my-2"
                ], "label" => "note"
            ])
            // ->add('state', ChoiceType::class, [
            //     'attr' => [
            //         "class" => "my-2",

            //     ], "label" => "state",
            //         "multiple" => true,
            //         "expanded" => true,
            //         "choices" => [
            //             "Disponible" => "Disponible",
            //             "En attente" => "En attente",
            //             "Loué" => "Loué"]

            // ])
            ->add('nbchambers', NumberType::class, [
                'attr' => [
                    "class" => "my-2"
                ], "label" => "chambers"
            ])
            ->add('nbbathrooms', NumberType::class, [
                'attr' => [
                    "class" => "my-2"
                ], "label" => "bathrooms"
            ])
            ->add('nbBeds', NumberType::class, [
                'attr' => [
                    "class" => "my-2"
                ], "label" => "beds "
            ])
            ->add('createdAt', DateType::class, [
                'attr' => ["class" => "my-2"],
                "label" => 'createdAt'
            ])
            ->add('updatedAt', DateType::class, [
                'attr' => ["class" => "my-2"],
                "label" => 'updatedAt'
            ])
            ->add('surface')
            ->add('bailleur', EntityType::class, [
                'attr' => [
                    "class" => "my-2"
                ], "label" => "bailleur",
                "class" => Professionnel::class,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('p')
                        ->join('p.responsable', 'u')
                        ->where('u.roles like :responsable')
                        ->setParameter('responsable', '%' . User::ROLE_BAILLEUR . '%');
                },
            ])
            ->add('appartPluses', EntityType::class, [
                "class" => AppartPlus::class,
                "choice_label" =>  function (AppartPlus $appart): string {
                    return $appart->__toString();
                },
                "multiple" => true,
                "expanded" => true,
                "label" => "pluses",
                "attr" => [
                    "class" => "my-2"
                ]
            ])
            ->add("images", FileType::class, [
                "multiple" => true,
                "mapped" => false,
                "label" => "images",
                "attr" => [
                    "class" => "my-2",
                    "accept" => "image/*"
                ],
                'constraints' => [
                    new All([
                        'constraints' => [
                            new Image([
                                'maxSize' => '5M',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                    'image/gif',
                                ],
                                'mimeTypesMessage' => 'Please upload a valid image',
                                'minWidth' => 20,
                                'minWidthMessage' => 'The image width is too small ({{ width }}px). Minimum width should be {{ min_width }}px.',
                                'minHeight' => 20,
                                'minHeightMessage' => 'The image height is too small ({{ height }}px). Minimum height should be {{ min_height }}px.',
                                'allowLandscape' => true,
                            ]),
                        ],
                    ]),
                ],
            ])


            ->add("submit", SubmitType::class, [
                "attr" => [
                    "class" => "btn btn-primary my-2"
                ], "label" => "submit"
            ]);
    }
}
