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

class WalkPrologueType extends AbstractType
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
            'create',
            SubmitType::class,
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

    public function getBlockPrefix()
    {
        return 'app_create_walk_prologue';
    }
}
