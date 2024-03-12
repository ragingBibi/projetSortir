<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\Venue;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ],
                'attr' => [
                    'class' => 'une-autre-classe'
                ]
            ])
            ->add('startingDateTime', DateTimeType::class, [
                'widget' => 'single_text',
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ]
            ])
            ->add('duration', DateIntervalType::class, [
                'widget' => 'single_text',
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ]
            ])
            ->add('maxAttendees', IntegerType::class, [
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ]
            ])
            ->add('details', TextType::class,  [
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ]
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('venue', EntityType::class, [
                'class' => Venue::class,
                'choice_label' => 'name'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save'
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
