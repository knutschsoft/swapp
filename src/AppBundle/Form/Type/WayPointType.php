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
                'label' => 'Bildupload',
                'attr' => [
                    'class' => 'btn btn-primary bg-silver',
                ]
            ]
        );
        $builder->add(
            'malesChildCount',
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
                'label' => 'Kinder m',
            )
        );
        $builder->add(
            'femalesChildCount',
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
                'label' => 'Kinder w',
            )
        );
        $builder->add(
            'malesKidCount',
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
                'label' => 'Kids m',
            )
        );
        $builder->add(
            'femalesKidCount',
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
                'label' => 'Kids w',
            )
        );
        $builder->add(
            'malesYouthCount',
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
                'label' => 'Jugendliche m',
            )
        );
        $builder->add(
            'femalesYouthCount',
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
                'label' => 'Jugendliche w',
            )
        );
        $builder->add(
            'malesAdultCount',
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
                'label' => 'Erwachsene m',
            )
        );
        $builder->add(
            'femalesAdultCount',
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
                'label' => 'Erwachsene w',
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
                'label' => 'speichern',
                'attr' => array('class' => 'btn btn-primary'),
            )
        );
        $builder->add(
            'createWalk',
            'submit',
            array(
                'label' => 'speichern, Runde abschließen',
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
