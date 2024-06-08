<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use App\Entity\User;
use Symfony\Component\Form\FormInterface;


class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your first name',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your last name',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email',
                    ]),
                    new Regex([
                        'pattern' => '/^[^\s@]{3,}@[^\s@]{3,}\.[^\s@]{2,}$/',
                        'message' => 'The email format should be xxx@yyy.zz'
                    ]),
                ],
            ])
            ->add('currentPassword', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your current password',
                    ]),
                    new UserPassword([
                        'message' => 'The current password is incorrect.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'mapped' => false,
                'first_options' => ['label' => 'New Password'],
                'second_options' => ['label' => 'Confirm New Password'],
                'invalid_message' => 'The password fields must match.',
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-zA-Z])(?=.*\d).+$/',
                        'message' => 'Your password must contain both letters and numbers'
                    ]),
                ],
                'validation_groups' => ['update'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => function (FormInterface $form) {
                $groups = ['Default'];
                if ($form->get('plainPassword')->getData()) {
                    $groups[] = 'update';
                }
                return $groups;
            },
        ]);
    }
}
