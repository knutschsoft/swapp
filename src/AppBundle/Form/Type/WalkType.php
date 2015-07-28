<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WalkType extends AbstractType
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
                'label' => 'Runenendzeit',
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
        return 'app_create_walk';
    }
}
