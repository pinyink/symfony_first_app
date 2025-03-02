<?php

namespace App\Form;

use App\Entity\Laman;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class LamanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
			->add('url', TextType::class, [
                'label' => 'URL'
            ])
			->add('name', TextType::class, [
                'label' => 'Title'
            ])
			->add('content', TextareaType::class, [
                'label' => 'Content',
                'attr' => [
                    'class' => 'summernote',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Laman::class,
        ]);
    }
}
        