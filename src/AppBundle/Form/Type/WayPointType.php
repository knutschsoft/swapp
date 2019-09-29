<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Tag;
use AppBundle\Entity\WayPoint;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class WayPointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'locationName',
            TextType::class,
            [
                'label' => 'Ort',
            ]
        );
        $builder->add(
            'imageFile',
            VichImageType::class,
            [
                'required' => false,
                'allow_delete' => true, // not mandatory, default is true
                'download_link' => true, // not mandatory, default is true
                'label' => 'Bildupload',
                'attr' => [
                    'class' => 'btn btn-primary bg-silver',
                ],
            ]
        );
        $builder->add(
            'ageGroups',
            AgeGroupType::class,
            [
                'data' => $options['data']->getAgeGroups(),
                'label' => 'Altersgruppen',
            ]
        );
        $builder->add(
            'note',
            TextareaType::class,
            [
                'label' => 'Beobachtung',
                'required' => false,
            ]
        );
        $builder->add(
            'wayPointTags',
            EntityType::class,
            [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
                'label' => 'Tags',
            ]
        );
        $builder->add(
            'isMeeting',
            CheckboxType::class,
            [
                'label' => 'mobiler Treff',
                'required' => false,
            ]
        );
        $builder->add(
            'createWayPoint',
            SubmitType::class,
            [
                'label' => 'speichern',
                'attr' => [
                    'class' => 'btn btn-primary',
                    'data-test' => 'save-way-point',
                ],
            ]
        );
        $builder->add(
            'createWalk',
            SubmitType::class,
            [
                'label' => 'speichern, Runde abschließen',
                'attr' => [
                    'class' => 'btn btn-primary',
                    'data-test' => 'save-and-finish-way-point',
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => WayPoint::class,
            ]
        );
    }
}
