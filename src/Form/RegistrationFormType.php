<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder



            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom',
                'required'      => true,
                'constraints' => new Length(['min' => 3]),
                'attr'  => [
                    'placeholder' => 'Merci de saisir votre prénom']
            ])

            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
                'required'      => true,
                'constraints' => new Length(['min' => 3]),
                'attr'  => [
                    'placeholder' => 'Merci de saisir votre nom']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'required'      => true,
                'constraints' => new Length(['min' => 3]),
                'attr'  => [
                    'placeholder' => 'Merci de saisir votre adresse email']
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,


                'attr' => ['autocomplete' => 'new-password',
                    'placeholder' =>  'Merci de saisir votre mot de passe'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit être au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
