<?php

namespace App\Form;

use App\Entity\Taille;
use App\DTO\SearchData;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
          
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'libel',
                'required' => false,
                'placeholder' => 'Toutes les catégories',
            ])
            ->add('taille', EntityType::class, [
                'class' => Taille::class,
                'choice_label' => 'libel',
                'required' => false,
                'placeholder' => 'Toutes les tailles',
            ])
            ->add('liquidation', ChoiceType::class, [
                'choices' => [
                    'Tous' => null,
                    'Oui' => true,
                    'Non' => false,
                ],
                'required' => false,
                'placeholder' => 'Liquidation',
            ])
            ->add('actif', ChoiceType::class, [
                'choices' => [
                    'Tous' => null,
                    'Oui' => true,
                    'Non' => false,
                ],
                'required' => false,
                'placeholder' => 'Actif',
            ])
            ->add('dateDebut', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Disponible à partir de',
            ])
            ->add('dateFin', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Jusqu\'à',
            ])
            ->add('stock', ChoiceType::class, [
                'choices' => [
                    'Tous' => null,
                    'Disponible' => true,
                    'Non disponible' => false,
                ],
                'required' => false,
                'label' => 'Disponibilité',
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}