<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WalkEpilogueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            'text',
            array(
                'label' => 'Name',
            )
        );
        $builder->add(
            'conceptOfDay',
            'textarea',
            array(
                'label' => 'Tageskonzept',
            )
        );
        $builder->add(
            'startTime',
            'datetime',
            array(
                'label' => 'Rundenstartzeit',
            )
        );
        $builder->add(
            'endTime',
            'datetime',
            array(
                'label' => 'Rundenendzeit',
            )
        );
        $builder->add(
            'walkReflection',
            'textarea',
            array(
                'label' => 'Reflexion',
            )
        );
        $builder->add(
            'systemicQuestion',
            'text',
            array(
                'label' => 'Systemische Frage',
                'read_only' => true,
            )
        );
        $builder->add(
            'systemicAnswer',
            'textarea',
            array(
                'label' => 'Systemische Antwort',
            )
        );
        $builder->add(
            'holidays',
            'text',
            array(
                'label' => 'Ferien',
            )
        );
        $builder->add(
            'weather',
            'text',
            array(
                'label' => 'Wetter',
            )
        );
        $builder->add(
            'rating',
            'choice',
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
            'checkbox',
            array(
                'label' => 'Ferien',
                'required' => false,
            )
        );
        $builder->add(
            'weather',
            'choice',
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
//        $builder->add(
//            'tags',
//            'choice',
//            array(
//                'choices' => array(),
//                'label' => 'Tags',
//            )
//        );
        $builder->add(
            'insights',
            'textarea',
            array(
                'label' => 'Erkenntnisse, Überlegungen, Zielsettungen',
            )
        );
        $builder->add(
            'commitments',
            'textarea',
            array(
                'label' => 'Termine, Besorgungen, Verabredungen',
            )
        );
        $builder->add(
            'isResubmission',
            'checkbox',
            array(
                'label' => 'Wiedervorlage Dienstberatung',
                'required' => false,
            )
        );
        $builder->add(
            'create',
            'submit',
            array(
                'label' => 'Runde abschliessen',
                'attr' => array('class' => 'btn btn-primary'),
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

    public function getName()
    {
        return 'app_create_walk_epilogue';
    }
}
