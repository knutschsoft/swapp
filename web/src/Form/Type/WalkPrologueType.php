<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Walk;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WalkPrologueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('id', HiddenType::class);
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'Name',
                'required' => true,
                'attr' => ['data-test' => 'Name'],
            ]
        );
        $builder->add(
            'conceptOfDay',
            TextareaType::class,
            [
                'label' => 'Tageskonzept',
                'attr' => ['data-test' => 'Tageskonzept'],
            ]
        );
        $builder->add(
            'startTime',
            DateTimeType::class,
            [
                'label' => 'Rundenstartzeit',
                'attr' => ['data-test' => 'Rundenstartzeit'],
                'help' => 'Die aktuelle Zeit ist vorausgewählt.',
                'widget' => 'single_text',
            ]
        );
        $builder->add(
            'holidays',
            CheckboxType::class,
            [
                'label' => 'Ferien',
                'required' => false,
                'attr' => ['data-test' => 'Ferien'],
            ]
        );
        $builder->add(
            'weather',
            ChoiceType::class,
            [
                'choices' => [
                    'Sonne' => 'Sonne',
                    'Wolken' => 'Wolken',
                    'Regen' => 'Regen',
                    'Schnee' => 'Schnee',
                    'Arschkalt' => 'Arschkalt',
                ],
                'placeholder' => '---',
                'label' => 'Wetter',
                'attr' => ['data-test' => 'Wetter'],
            ]
        );
        $builder->add(
            'create',
            SubmitType::class,
            [
                'label' => 'Runde beginnen',
                'attr' => [
                    'class' => 'btn btn-secondary btn-block',
                    'data-test' => 'create-way-point',
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->resolve(
            [
                'data_class' => Walk::class,
            ]
        );
        $resolver->setDefaults(
            [
                'validation_groups' => ['prologue'],
            ]
        );
    }
}
