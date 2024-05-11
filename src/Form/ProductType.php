<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Product;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
			->add('nama', TextType::class, [
                'label' => 'Nama'
            ])
			->add('harga', TextType::class, [
                'label' => 'Harga',
                'attr' => [
                    'onkeyup' => 'formatRupiah("product_harga")',
                    'onkeydown' => 'formatRupiah("product_harga")'
                ]
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'query_builder' => function (CategoriesRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
        