<?php
namespace AppBundle\Form\Type;

use AppBundle\Repository\DoctrineORMTagRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WalkType extends AbstractType
{
    private $tagRepository;

    /**
     * @param DoctrineORMTagRepository $tagRepository
     */
    public function __construct(DoctrineORMTagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

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
//                'choice_loader' => $this->tagRepository->getTags(),
//                'choices_as_values' => true,
//                'multiple' => true,
//                'expanded' => true,
//                'choice_label' => function ($tag, $key) {
//                        return $tag->getName();
//                },
//                'choice_value' => function ($tag) {
//
//                    if (get_class($tag) != 'Doctrine\ORM\PersistentCollection') {
//                        return $tag->getId();
//                    }
//                },
//
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
                'label' => 'Runde abschließen',
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
