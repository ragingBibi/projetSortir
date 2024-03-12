<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
                    'constraints' => [
                        new NotBlank([
                            'message' => 'La saisie de l\'email est obligatoire'],
                        )
                    ]]
            )
            ->add('lastname', TextType::class, [
                'constraints' => [
                    /*new Regex([
                        'pattern' => '^([a-zA-Z]{2,50})$',
                        'message' => 'Votre nom ne doit pas comporter de caractères spéciaux ou numériques'
                    ])*/
                ],
                'row_attr' => [
                    'class' => 'input-groupe mb-3']
            ])
            ->add('firstName', TextType::class, [
                'row_attr' => [
                    'class' => 'input-groupe mb-3']
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('phoneNumber', TextType::class, [
                'row_attr' => ['class' => 'input-groupe mb-3'],
                /*'constraints' => [
                    new Regex([
                        'pattern' => '^([0-9]{10})$',
                        'message' => 'Veuillez saisir un numéro de téléphone valide'])
                ]
            ])*/])
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => 'name',
                'multiple' => false,
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
