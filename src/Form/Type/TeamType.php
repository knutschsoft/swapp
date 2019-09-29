<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'Name',
                'required' => true,
            ]
        );
        $builder->add(
            'ageRanges',
            CollectionType::class,
            [
                'entry_type' => AgeRangeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'attr' => [
                    'class' => 'js-team-type-age-ranges',
                ],
                'label' => 'Altersbereiche',
            ]
        );
        $builder->add(
            'users',
            EntityType::class,
            [
                'class' => User::class,
                'choice_label' => static function (User $user) {
                    return $user->getUsername();
                },
                'multiple' => true,
                'expanded' => true,
                'label' => 'Mitglieder',
                'required' => true,
                'placeholder' => '---',
                'by_reference' => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Team::class,
            ]
        );
    }
}
