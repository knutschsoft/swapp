<?php
namespace AppBundle\Form\Type;

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

class WalkType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'id',
            HiddenType::class
        );
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
            )
        );
        $builder->add(
            'systemicQuestion',
            TextType::class,
            [
                'label' => 'Systemische Frage',
                'disabled' => true,
            ]
        );
        $builder->add(
            'systemicAnswer',
            TextareaType::class,
            array(
                'label' => 'Systemische Antwort',
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
            array(
                'choices' => array(
                    'Sonne' => 'Sonne',
                    'Wolken' => 'Wolken',
                    'Regen' => 'Regen',
                    'Schnee' => 'Schnee',
                    'Arschkalt' => 'Arschkalt',
                ),
                'label' => 'Wetter',
            )
        );
        $builder->add(
            'insights',
            TextareaType::class,
            array(
                'label' => 'Erkenntnisse, Überlegungen, Zielsetzungen',
            )
        );
        $builder->add(
            'commitments',
            TextareaType::class,
            array(
                'label' => 'Termine, Besorgungen, Verabredungen',
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
        $builder->add(
            'create',
            SubmitType::class,
            array(
                'label' => 'Runde abschließen',
                'attr' =>
                    [
                        'class' => 'btn btn-primary',
                        'data-test' => 'create-walk',
                    ],
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Walk',
            )
        );
    }

    public function getBlockPrefix()
    {
        return 'app_create_walk';
    }
}
