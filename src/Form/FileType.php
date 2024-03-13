<?php

namespace App\Form;

use App\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType as FileTypeType;
use Symfony\Component\Validator\Constraints\File as FileConstraints;

class FileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
			->add('name', TextType::class, [
                'label' => 'Name'
            ])
			->add('size', TextType::class, [
                'label' => 'Size',
                'attr' => [
                    'readonly' => 'readonly'
                ]
            ])
			->add('path', FileTypeType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new FileConstraints([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image'
                    ])
                ],
                'attr' => [
                    'accept' => '.jpg,.jpeg,.img,.png',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => File::class,
        ]);
    }
}
        