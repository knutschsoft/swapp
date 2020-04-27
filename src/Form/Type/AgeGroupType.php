<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Value\AgeGroup;
use App\Value\AgeRange;
use App\Value\Gender;
use App\Value\PeopleCount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgeGroupType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $ageGroups = $options['data'];

        $choices = [
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
            '11' => '11',
            '12' => '12',
            '13' => '13',
            '14' => '14',
            '15' => '15',
            '16' => '16',
            '17' => '17',
            '18' => '18',
            '19' => '19',
            '20' => '20',
        ];

        /** @var AgeGroup $ageGroup */
        foreach ($ageGroups as $ageGroup) {
            $builder->add(
                $this->getChildName($ageGroup),
                ChoiceType::class,
                [
                    'choices' => $choices,
                    'required' => true,
                    'label' => \sprintf(
                        '%s - %s %s',
                        $ageGroup->ageRange()->getRangeStart(),
                        $ageGroup->ageRange()->getRangeEnd(),
                        $ageGroup->gender()->gender
                    ),
                ]
            );
        }

        $builder->setDataMapper($this);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => null,
            ]
        );
    }

    /**
     * @param mixed                        $data
     * @param FormInterface[]|\Traversable $forms
     */
    public function mapDataToForms($data, $forms): void
    {
        if (null === $data) {
            return;
        }

        $forms = \iterator_to_array($forms);

        foreach ($forms as $childName => $form) {
            $ageGroup = $this->getAgeGroupFromChildName($childName, $form);

            /** @var AgeGroup $ageGroupData */
            foreach ($data as $ageGroupData) {
                if ($ageGroupData->equalType($ageGroup)) {
                    $form->setData($ageGroupData->peopleCount()->count());
                }
            }
        }
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     * @param mixed                        $data  Structured data
     *
     * @throws Exception\UnexpectedTypeException if the type of the data parameter is not supported
     */
    public function mapFormsToData($forms, &$data): void
    {
        if (null === $data) {
            return;
        }

        $forms = \iterator_to_array($forms);

        $ageGroups = [];
        foreach ($forms as $childName => $form) {
            $ageGroup = $this->getAgeGroupFromChildName($childName, $form);
            $ageGroups[] = $ageGroup;
        }
        $data = $ageGroups;
    }

    private function getChildName(AgeGroup $ageGroup): string
    {
        return $ageGroup->gender()->gender().$ageGroup->ageRange()->getRangeStart().'to'.$ageGroup->ageRange()->getRangeEnd();
    }

    private function getAgeGroupFromChildName(string $childName, Form $form): AgeGroup
    {
        $gender = Gender::fromString(\substr($childName, 0, 1));
        $rangeArray = \explode('to', \substr($childName, 1, \strlen($childName)));
        $range = AgeRange::fromArray($rangeArray);
        $peopleCount = PeopleCount::fromInt((int) $form->getData());

        return AgeGroup::fromRangeGenderAndCount($range, $gender, $peopleCount);
    }
}
