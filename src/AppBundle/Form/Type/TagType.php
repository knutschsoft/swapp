<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            array(
                'label' => 'Name',
            )
        );
        $builder->add(
            'color',
            TextType::class,
            array(
                'label' => 'Farbe',
            )
        );
        $builder->add(
            'create',
            SubmitType::class,
            array(
                'label' => 'Tag erstellen',
                'attr' => array('class' => 'btn btn-primary'),
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Tag',
            )
        );
    }

    public function getBlockPrefix()
    {
        return 'app_create_tag';
    }
}
