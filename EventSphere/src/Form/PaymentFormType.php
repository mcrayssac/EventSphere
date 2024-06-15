<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cardNumber', TextType::class, [
                'label' => 'Card Number',
                'attr' => ['placeholder' => '1234 1234 1234 1234'],
            ])
            ->add('expMonth', TextType::class, [
                'label' => 'Expiration Month',
                'attr' => ['placeholder' => 'MM'],
            ])
            ->add('expYear', TextType::class, [
                'label' => 'Expiration Year',
                'attr' => ['placeholder' => 'YYYY'],
            ])
            ->add('cvc', TextType::class, [
                'label' => 'CVC',
                'attr' => ['placeholder' => 'CVC'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
