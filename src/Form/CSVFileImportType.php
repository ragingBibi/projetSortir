<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CSVFileImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('csv_file', FileType::class, [
            'label' => 'Importer un fichier .CSV',
            'row_attr' => [
                'class' => 'col-md-8 mb-3'],
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File([
                    'mimeTypes' => [
                        'text/csv',
                    ],
                    'mimeTypesMessage' => 'Le format du fichier doit être csv',
                ]),
            ]]);

        $builder->add("submit", SubmitType::class, [
            'label' => 'Soumettre le fichier'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
