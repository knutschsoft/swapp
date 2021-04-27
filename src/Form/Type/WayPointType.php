<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Tag;
use App\Entity\WayPoint;
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
                'label' => 'Wegpunkt speichern',
                'attr' => [
                    'class' => 'btn-secondary mb-0 btn-block',
                    'data-test' => 'save-way-point',
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
