<?php

namespace App\Form;

use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class FigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Nom de la figure',
                'required'      => true,
                'constraints' => new Length(['min' => 3]),
                'attr'  => [
                    'placeholder' => 'Merci de saisir le nom de la figure']
            ])

            ->add('slug')

            ->add('imageFile', FileType::class,[
                'required' =>false,

            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required'      => true,
                'constraints' => new Length(['min' => 3]),
                'attr'  => [
                    'placeholder' => 'Merci de saisir une description pour la figure']
            ])
            ->add('category')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
