<?php

namespace App\Form;

use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_rdv', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date du rendez-vous',
                                'constraints' => [
                    new NotBlank([
                        'message' => 'La date du rendez-vous est obligatoire.',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => (new \DateTime())->format('Y-m-d'),
                        'message' => 'La date du rendez-vous ne peut pas être antérieure à aujourd\'hui.',
                    ]),
                ],
            ])
            ->add('heure_rdv', ChoiceType::class, [
                'choices' => [],
                'placeholder' => 'Choisissez une heure',
                'required' => true,
                'mapped' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'email est obligatoire.',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le prénom est obligatoire.',
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom est obligatoire.',
                    ]),
                ],
            ])
            ->add('tel', IntegerType::class, [
                'label' => 'Téléphone',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le téléphone est obligatoire.',
                    ]),
                ],
            ])
            ->add('date_evenemnt', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de l\'événement',
                'attr' => [
                    'id' => 'date_evenemnt', // Ajout de l'ID pour la sélection via FullCalendar
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La date de l\'événement est obligatoire.',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => (new \DateTime())->format('Y-m-d'),
                        'message' => 'La date de l\'événement ne peut pas être antérieure à aujourd\'hui.',
                    ]),
                ],
            ])
            ->add('type_evenement', ChoiceType::class, [
                'choices' => [
                    'Marié' => 'marié',
                    'Invité' => 'invité',
                    'Marié sur mesure' => 'marié_sur_mesure',
                ],
                'label' => 'Type d\'événement',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le type d\'événement est obligatoire.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}