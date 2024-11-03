<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Publisher;
use App\Repository\PublisherRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType as FileTypeType;
use Symfony\Component\Validator\Constraints\File as FileConstraints;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
			->add('name', TextType::class, [
                'label' => 'Name Book'
            ])
			->add('cover', FileTypeType::class, [
                'label' => 'cover',
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
			->add('file', FileTypeType::class, [
                'label' => 'file',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new FileConstraints([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF'
                    ])
                ],
                'attr' => [
                    'accept' => '.pdf',
                ]
            ])
            ->add('author', EntityType::class, [
                'class' => Publisher::class,
                'query_builder' => function (PublisherRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
                'placeholder' => 'Pilih Kategori',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
        