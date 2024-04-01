<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
        