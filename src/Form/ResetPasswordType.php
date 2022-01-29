<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('new_password', RepeatedType::class, [
        'type'          => PasswordType::class,
        'invalid_message' => 'Le mot de passe et la confirmation doivent être identique.',
        'label'         => 'Votre mot de passe',
        'required'      => true,
        'constraints' => new Length(['min' => 6]),
        'first_options' => ['label' => 'Votre nouveau Mot de passe',
            'attr' => [
                'placeholder' =>  'Merci de saisir votre nouveau mot de passe'
            ]
        ],
        'second_options'=> ['label' => 'Confirmez votre nouveau mot de passe',

            'attr' => [
                'placeholder' =>  'Merci de confirmez votre nouveau mot de passe'
            ]]

    ])

        ->add('submit', SubmitType::class, [
            'label' => "Mettre à jour mon mot de passe",
            'attr'  =>[
                'class' => 'btn-block btn-info'
            ]
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
