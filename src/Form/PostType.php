<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Post;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputClass = 'w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-white placeholder-gray-400 focus:border-indigo-500 focus:outline-none';

        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'attr' => ['class' => $inputClass],
            ])
            ->add('excerpt', TextareaType::class, [
                'label' => 'Excerpt',
                'required' => false,
                'attr' => ['class' => $inputClass, 'rows' => 2],
            ])
            ->add('body', TextareaType::class, [
                'label' => 'Body',
                'attr' => ['class' => $inputClass, 'rows' => 8],
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => ['Draft' => 'draft', 'Published' => 'published'],
                'attr' => ['class' => $inputClass],
            ])
            ->add('author', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => '— Select author —',
                'label' => 'Author',
                'attr' => ['class' => $inputClass],
            ])
            ->add('coauthor', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => '— Select co-author —',
                'label' => 'Co-author',
                'attr' => ['class' => $inputClass],
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'label' => 'Tags',
                'attr' => ['class' => $inputClass, 'size' => 5],
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
