<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('dateDebut', DateType::class, [
            'widget' => 'single_text',
            'label' => 'Date de début',
            'attr' => [
                'class' => 'js-datepicker',
                'id' => 'reservation_date_debut'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'La date de début est obligatoire.',
                ]),
                new GreaterThanOrEqual([
                    'value' => 'today',
                    'message' => 'La date de début ne peut pas être antérieure à aujourd\'hui.',
                ]),
            ],
        ])
        ->add('dateFin', DateType::class, [
            'widget' => 'single_text',
            'label' => 'Date de fin',
           
        ])
        ->add('clientEmail', EmailType::class, [
            'mapped' => false,
            'label' => 'Email du client',
            'constraints' => [
                new NotBlank([
                    'message' => 'L\'email est obligatoire.',
                ]),
            ],
        ])
        ->add('clientPrenom', TextType::class, [
            'mapped' => false,
            'label' => 'Prénom du client',
            'constraints' => [
                new NotBlank([
                    'message' => 'Le prénom est obligatoire.',
                ]),
            ],
        ])
        ->add('clientNom', TextType::class, [
            'mapped' => false,
            'label' => 'Nom du client',
            'constraints' => [
                new NotBlank([
                    'message' => 'Le nom est obligatoire.',
                ]),
            ],
        ])
        ->add('clientTelephone', TextType::class, [
            'mapped' => false,
            'label' => 'Téléphone du client',
            'constraints' => [
                new NotBlank([
                    'message' => 'Le téléphone est obligatoire.',
                ]),
            ],
        ])
        ->add('depotType', ChoiceType::class, [
            'label' => 'Type de dépôt',
            'required' => false,
            'choices' => [
                'Aucun' => null,
                'Espèces' => 'especes',
                'Carte bancaire' => 'cb',
                'Chèque' => 'cheque',
            ],
            'mapped' => false,
        ])
        ->add('depotMontant', MoneyType::class, [
            'label' => 'Montant du dépôt',
            'required' => false,
            'currency' => 'EUR',
            'mapped' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}