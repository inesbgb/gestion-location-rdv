<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\ElementAdmin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ElementAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('carouselImage1', EntityType::class, [
            'class' => Produit::class,
            'choice_label' => 'designation', // Assurez-vous que 'designation' est une propriété de Produit
            'placeholder' => 'Choisir une image pour le carrousel 1',
            'required' => false,
        ])
        ->add('carouselImage2', EntityType::class, [
            'class' => Produit::class,
            'choice_label' => 'designation',
            'placeholder' => 'Choisir une image pour le carrousel 2',
            'required' => false,
        ])
        ->add('carouselImage3', EntityType::class, [
            'class' => Produit::class,
            'choice_label' => 'designation',
            'placeholder' => 'Choisir une image pour le carrousel 3',
            'required' => false,
        ])
        ->add('carouselImage4', EntityType::class, [
            'class' => Produit::class,
            'choice_label' => 'designation',
            'placeholder' => 'Choisir une image pour le carrousel 4',
            'required' => false,
        ])
        ->add('carouselImage5', EntityType::class, [
            'class' => Produit::class,
            'choice_label' => 'designation',
            'placeholder' => 'Choisir une image pour le carrousel 5',
            'required' => false,
        ])
        ->add('carouselImage6', EntityType::class, [
            'class' => Produit::class,
            'choice_label' => 'designation',
            'placeholder' => 'Choisir une image pour le carrousel 6',
            'required' => false,
        ])
            ->add('imageHistoire', FileType::class, [
                'label' => 'Image histoire',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide',
                    ])
                ],
            ])
            ->add('videoFile', FileType::class, [
                'label' => 'Vidéo',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '100M',
                        'mimeTypes' => [
                            'video/mp4',
                            'video/avi',
                            'video/mpeg',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une vidéo valide',
                    ])
                ],
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ElementAdmin::class,
        ]);
    }
}
