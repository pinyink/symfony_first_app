<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
			->add('url', TextType::class, [
                'label' => 'URL'
            ])
			->add('title', TextType::class, [
                'label' => 'Title'
            ])
			->add('summary', TextareaType::class, [
                'label' => 'Summary'
            ])
			->add('content', TextareaType::class, [
                'label' => 'Content',
                'attr' => [
                    'class' => 'summernote',
                ]
            ])
            ->add('publish', ChoiceType::class, [
                'choices' => [
                    'Pilih Satu' => '',
                    'Ya' => '1',
                    'Tidak' => '0'
                ],
                'mapped' => true,
                'label' => 'Publish',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
        