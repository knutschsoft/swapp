<?php
declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Walk;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class WalksTimeRangeFilter extends AbstractFilter
{
    public function getDescription(string $resourceClass): array
    {
        // This function is only used to hook in documentation generators (supported by Swagger and Hydra)
        if (!$this->properties) {
            return [];
        }

        $description = [];
        // phpcs:ignore
        foreach ($this->properties as $property => $strategy) {
            $description["walks.$property"] = [
                'property' => $property,
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'description' => 'Filter using date range on startDate of walks.',
                'openapi' => [
                    'example' => '01.10.2021..31.12.2022',
                ],
            ];
        }

        return $description;
    }

    /** @inheritDoc */
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        $resourceClass = Walk::class;

        if (!\str_starts_with($property, 'walks.') || !\is_string($value)) {
            return;
        }
        $property = \str_replace('walks.', '', $property);

        if ('timeRange' !== $property) {
            return;
        }

        $values = \explode('..', $value);
        if (2 !== \count($values)) {
            return;
        }
        if (!$this->isPropertyEnabled($property, $resourceClass) ||
            !$this->isPropertyMapped('startTime', $resourceClass)
        ) {
            return;
        }
        $property = 'startTime';

        $queryBuilder
            ->distinct(true)
            ->join('o.walks', 'w')
            ->andWhere(\sprintf('w.%s > :timeFrom', $property))
            ->setParameter('timeFrom', $values[0])
            ->andWhere(\sprintf('w.%s < :timeTo', $property))
            ->setParameter('timeTo', $values[1]);
    }
}
