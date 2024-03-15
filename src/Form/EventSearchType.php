<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\EventSearch;
use App\Entity\Event;
use App\Model\SearchData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
                    'placeholder' => 'Recherche par mot clé',
                    'class' => 'form-control'
                ]
            ])
            ->add('campus', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Choix du campus',
                'choices' => [
                    'NANTES' => 'NANTES',
                    'NIORT' => 'NIORT',
                    'RENNES' => 'RENNES',
                    'QUIMPER' => 'QUIMPER',
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
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

    /**
     * Cette fonction permet d'épurer l'URL de recherche
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
