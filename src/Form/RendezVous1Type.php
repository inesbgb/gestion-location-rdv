<?php

namespace App\Form;


use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class RendezVous1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('date_rdv', DateType::class, [
            'widget' => 'single_text',
            'label' => 'Date du rendez-vous',
            'html5' => false,
            'attr' => [
                'min' => (new \DateTime())->format('Y-m-d'),
            ],
            'constraints' => [
                new GreaterThanOrEqual([
                    'value' => new \DateTime(),
                    'message' => 'La date du rendez-vous ne peut pas être antérieure à aujourd\'hui.',
                ]),
            ],
        ])
        ->add('heure_rdv', TimeType::class, [
            'widget' => 'choice',
            'label' => 'Heure du rendez-vous',
            'hours' => range(10, 19), // Heures de 10h à 19h
            'minutes' => [0], // Seulement les minutes 00
        ])
        ->add('statut', ChoiceType::class, [
            'label' => 'Statut',
            'choices' => [
                'Confirmé' => true,
                'Annulé' => false,
            ],
        ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('tel', IntegerType::class, [
                'label' => 'Téléphone',
            ])
            ->add('date_evenemnt', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de l\'événement',
                'html5' => true,
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'),
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => new \DateTime(),
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
                
            ])
            ->add('num_rdv', IntegerType::class, [
                'label' => 'Numéro unique de rendez-vous',
                'disabled' => true,
                'attr' => ['readonly' => true],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}
