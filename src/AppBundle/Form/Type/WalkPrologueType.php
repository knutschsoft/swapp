<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WalkPrologueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'id',
            'hidden'
        );
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
        $builder->add(
            'create',
            'submit',
            array(
                'label' => 'Wegpunkt anlegen',
                'attr' => array('class' => 'btn btn-primary'),
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->resolve(
            array(
                'data_class' => 'AppBundle\Entity\Walk',
                'validation_groups' => array('prologue'),
            )
        );
    }

    public function getName()
    {
        return 'app_create_walk_prologue';
    }
}
