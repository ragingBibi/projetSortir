<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                    'label' => 'Adresse email',
/*                    'row_attr' => [
                        'class' => 'col-md-12 mb-3'],*/
                    'constraints' => [
                        new NotBlank([
                            'message' => 'La saisie de l\'email est obligatoire'],
                        )
                    ]]
            )
            ->add('picture_file', FileType::class, [
                'label' => 'Photo de profil',
/*                'row_attr' => [
                    'class' => 'col-md-2 mb-3'],*/
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '500k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'maxSizeMessage' => 'La taille maximale est de 500ko.',
                        'mimeTypesMessage' => 'Le format de l\'image doit être jpeg, jpg ou png',
                    ]),
                ]])
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
/*                'row_attr' => [
                    'class' => 'col-md-12 mb-3'],*/
                'constraints' => [
                    /**
                     * new Regex([
                     * 'pattern' => '^([a-zA-Z0-9-_@.]{3,20})$',
                     * 'message' => 'Le pseudo doit contenir entre 3 et 20 caractères alphanumériques (caractères spéciaux autorisés: . - _ et @)'
                     * ])**/
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
/*                'row_attr' => [
                    'class' => 'col-md-6 mb-3'],*/
                'constraints' => [
                    /*new Regex([
                        'pattern' => '^([a-zA-Z]{2,50})$',
                        'message' => 'Votre nom ne doit pas comporter de caractères spéciaux ou numériques'
                    ])*/
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
/*                'row_attr' => [
                    'class' => 'col-md-6 mb-3']*/
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir un mot de passe',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                ],
                'invalid_message' => 'Les mots de passe ne sont pas identiques',
            ])
            ->add('phoneNumber', TextType::class, [
/*                'row_attr' => [
                    'class' => 'col-md-12 mb-3'],*/
                'label' => 'Téléphone',
                /*'constraints' => [
                    new Regex([
                        'pattern' => '^([0-9]{10})$',
                        'message' => 'Veuillez saisir un numéro de téléphone valide'])
                ]
            ])*/
            ])
            ->add('campus', EntityType::class, [
                'label' => 'Sélection campus',
                'class' => Campus::class,
                'choice_label' => 'name',
                'multiple' => false,
            ])
            ->add("submit", SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
        /**
         * ->add('isAdmin', EntityType::class,[
         * 'class' => User::class,
         * 'multiple' => false,
         * 'expanded' => false
         * ]);
         **/

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
