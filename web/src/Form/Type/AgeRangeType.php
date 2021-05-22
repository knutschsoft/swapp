<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Value\AgeRange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

class AgeRangeType extends AbstractType implements DataMapperInterface
{
    // @codingStandardsIgnoreStart
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'rangeStart',
            IntegerType::class,
            [
                'required' => true,
            ]
        );
        $builder->add(
            'rangeEnd',
            IntegerType::class,
            [
                'required' => true,
            ]
        );

        $builder->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => null,
            ]
        );
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param AgeRange|mixed|null          $data  Structured data
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     *
     * @throws Exception\UnexpectedTypeException if the type of the data parameter is not supported
     */
    public function mapDataToForms($data, $forms): void
    {
        if (null === $data) {
            return;
        }

        $forms = \iterator_to_array($forms);

        Assert::isInstanceOf($data, AgeRange::class);
        $forms['rangeStart']->setData($data->getRangeStart());
        $forms['rangeEnd']->setData($data->getRangeEnd());
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     * @param mixed                        $data  Structured data
     *
     * @throws Exception\UnexpectedTypeException if the type of the data parameter is not supported
     */
    public function mapFormsToData($forms, &$data): void // @codingStandardsIgnoreLine
    {
        if (null === $data) {
            return;
        }

        $forms = \iterator_to_array($forms);
        $data = AgeRange::fromArray(
            [
                0 => $forms['rangeStart']->getData(),
                1 => $forms['rangeEnd']->getData(),
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'AgeRangeType';
    }
    // @codingStandardsIgnoreEnd
}
