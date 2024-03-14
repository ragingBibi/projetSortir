<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EventDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cancellationReason', TextType::class,  [
                'label' => 'Motif de l\'annulation',
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Supprimer',
                'attr' => [
                    'class' => 'btn btn-danger',
                ]
            ]);
        ;
    }

}