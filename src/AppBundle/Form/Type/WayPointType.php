<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WayPointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'locationName',
            'text',
            array(
                'label' => 'Name',
            )
        );
        $builder->add(
            'imageFile',
            'vich_image',
            [
                'required' => false,
                'allow_delete' => true, // not mandatory, default is true
                'download_link' => true, // not mandatory, default is true
                'label' => 'Bildupload'
            ]
        );
        $builder->add(
            'ageRangeStart',
            'text',
            array(
                'label' => 'Alter ab:',
            )
        );
        $builder->add(
            'ageRangeEnd',
            'text',
            array(
                'label' => 'Alter bis:',
            )
        );
        $builder->add(
            'malesCount',
            'choice',
            array(
                'choices' => array(
                    '0' => '0',
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                    '9' => '9',
                    '10' => '10',
                ),
                'required' => true,
                'label' => 'Anzahl Männer',
            )
        );
        $builder->add(
            'femalesCount',
            'choice',
            array(
                'choices' => array(
                    '0' => '0',
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                    '9' => '9',
                    '10' => '10',
                ),
                'required' => true,
                'label' => 'Anzahl Frauen',
            )
        );
        $builder->add(
            'note',
            'textarea',
            array(
                'label' => 'Notiz',
            )
        );
        $builder->add(
            'isMeeting',
            'checkbox',
            array(
                'label' => 'Meeting',
                'required' => false,
            )
        );
        $builder->add(
            'createWayPoint',
            'submit',
            array(
                'label' => 'neuer Wegpunkt',
                'attr' => array('class' => 'btn btn-primary'),
            )
        );
        $builder->add(
            'createWalk',
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
                'data_class' => 'AppBundle\Entity\WayPoint',
            )
        );
    }

    public function getName()
    {
        return 'app_create_way_point';
    }
}
