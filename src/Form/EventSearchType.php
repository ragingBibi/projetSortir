<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Event;
use App\Model\SearchData;
use App\Repository\CampusRepository;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_value' => 'id',
                'query_builder' => function (CampusRepository $er): QueryBuilder {
                return $er->createQueryBuilder('c')
                    ->orderBy('c.name', 'ASC');
                },
                'required' => false,
                'label' => false,
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Choix du campus',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('beginDateTime', DateTimeType::class, [
                'required' => false,
                'label' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]

            ])
            ->add('endDateTime', DateTimeType::class, [
                'required' => false,
                'label' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('isOrganizer', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties dont je suis l\'organisateur',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('isParticipant', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties auxquelles je suis inscrit',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('isNotParticipant', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties auxquelles je ne suis pas inscrit',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('passedDateTime', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties passées',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
        ;

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
