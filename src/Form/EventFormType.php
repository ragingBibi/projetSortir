<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\Venue;
use PHPUnit\Framework\Constraint\GreaterThan;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use function Symfony\Component\Clock\now;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'evenement',
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ],
                'attr' => [
                    'class' => 'une-autre-classe'
                ]
            ])
            ->add('startingDateTime', DateTimeType::class, [
                'label' => 'Date et heure',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'input-group mb-3'
                ],
            ])
            ->add('duration', DateIntervalType::class, [
                'label' => 'Duree',
                'required' => true,
                'labels' => [
                    'days' => 'Jours',
                    'hours' => 'Heures',
                    'minutes' => 'Minutes'
                ],
                'widget' => 'choice',
                'with_years' => false,
                'with_months' => false,
                'with_days' => true,
                'with_hours' => true,
                'hours' => range(1, 24),
                'with_minutes' => true,
                'minutes' => range(1, 60),
                'row_attr' => [
                    'class' => 'input-group mb-3 duration-fields'
                ]
            ])
            ->add('maxAttendees', IntegerType::class, [
                'label' => 'Nombre maximum d\'inscrits',
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ]
            ])
            ->add('registrationDeadline', DateTimeType::class, [
                'label' => 'Date limite d\'inscription',
                'widget' => 'single_text',
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ]
            ])
            ->add('details', TextType::class,  [
                'label' => 'Description',
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ]
            ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'placeholder' => '-- Choisir un campus --',
                'class' => Campus::class,
                'choice_label' => 'name',
                'multiple' => false,
            ])
            ->add('venue', EntityType::class, [
                'label' => 'Lieu',
                'placeholder' => '-- Choisir un lieu --',
                'class' => Venue::class,
                'choice_label' => 'name'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
