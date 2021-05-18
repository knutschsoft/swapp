<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Walk;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WalkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'name',
            TextType::class,
            array(
                'label' => 'Name',
            )
        );
        $builder->add(
            'conceptOfDay',
            TextareaType::class,
            array(
                'label' => 'Tageskonzept',
            )
        );
        $builder->add(
            'startTime',
            DateTimeType::class,
            array(
                'label' => 'Rundenstartzeit',
            )
        );
        $builder->add(
            'endTime',
            DateTimeType::class,
            array(
                'label' => 'Rundenendzeit',
            )
        );
        $builder->add(
            'walkReflection',
            TextareaType::class,
            array(
                'label' => 'Reflexion',
                'attr' => ['data-test' => 'Reflexion'],
            )
        );
        $builder->add(
            'systemicQuestion',
            TextareaType::class,
            [
                'label' => 'Systemische Frage',
                'disabled' => true,
                'attr' => [
                    'style' => 'width:100%;',
                    'data-test' => 'Systemische Frage'
                ],
            ]
        );
        $builder->add(
            'systemicAnswer',
            TextareaType::class,
            array(
                'label' => 'Systemische Antwort',
                'attr' => ['data-test' => 'Systemische Antwort'],
            )
        );
        $builder->add(
            'holidays',
            TextType::class,
            array(
                'label' => 'Ferien',
            )
        );
        $builder->add(
            'rating',
            ChoiceType::class,
            array(
                'choices' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ),
                'required' => true,
                'label' => 'Rundenbewertung',
            )
        );
        $builder->add(
            'holidays',
            CheckboxType::class,
            array(
                'label' => 'Ferien',
                'required' => false,
            )
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
            ]
        );
        $builder->add(
            'insights',
            TextareaType::class,
            array(
                'label' => 'Erkenntnisse, Überlegungen, Zielsetzungen',
                'attr' => ['data-test' => 'insights'],
            )
        );
        $builder->add(
            'commitments',
            TextareaType::class,
            array(
                'label' => 'Termine, Besorgungen, Verabredungen',
                'attr' => ['data-test' => 'commitments'],
            )
        );
        $builder->add(
            'isResubmission',
            CheckboxType::class,
            array(
                'label' => 'Wiedervorlage Dienstberatung',
                'required' => false,
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Walk::class,
            ]
        );
        $resolver->setDefaults(
            [
                'validation_groups' => ['prologue', 'registration'],
            ]
        );
    }
}
