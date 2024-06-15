<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('dateTime', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('maxParticipants', IntegerType::class)
            ->add('isPublic', CheckboxType::class, [
                'required' => false,
                'label' => 'Public Event'
            ])
            ->add('isPaid', CheckboxType::class, [
                'label' => 'Is this event paid?',
                'required' => false,
            ])
            ->add('cost', MoneyType::class, [
                'label' => 'Cost',
                'currency' => 'USD',
                'required' => false,
                'attr' => ['placeholder' => 'Enter cost if paid'],
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}

