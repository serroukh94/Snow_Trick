<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Figures;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiguresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Le titre',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Veuillez saisir le titre'
                ]
            ])

            ->add('description', TextareaType::class, [
                'label' => 'La description',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Veuillez saisir le contenu'
                ]
            ])

            ->add('category', EntityType::class, [
                'label' => 'La catégorie',
                'required' => true,
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false
            ])

            ->add('images', FileType::class,[
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])

            ->add('videos', UrlType::class, [
                'row_attr' => [
                    'class' => 'input-group',
                ],
                'label' => false,
                'mapped' => false,
                'required' => false,
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figures::class,
        ]);
    }
}
