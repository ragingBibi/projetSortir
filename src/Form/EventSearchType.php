<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\EventSearch;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('keyword', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Saisir un mot clé',
                ]
            ])
            ->add('maxAttendees', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nombre max d\'inscrits',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventSearch::class,
            'csrf_protection' => false,
            'method' => 'GET',
        ]);
    }

    /**
     * Cette fonction permet d'épurer l'URL de recherche
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
