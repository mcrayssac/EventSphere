<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a title',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a description',
                    ]),
                ],
            ])
            ->add('dateTime', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a date and time',
                    ]),
                    new GreaterThan([
                        'value' => 'now',
                        'message' => 'The date must be in the future',
                    ]),
                ],
            ])
            ->add('maxParticipants', IntegerType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the maximum number of participants',
                    ]),
                ],
            ])
            ->add('isPublic', CheckboxType::class, [
                'required' => false,
                'label' => 'Public Event'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}

