<?php
declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Export\WayPointExport;
use App\Entity\WayPoint;

class WayPointExportProvider implements ProviderInterface
{
    public function __construct(
        private readonly CollectionProvider $collectionProvider,
        private readonly WayPointExportDataTransformer $wayPointExportDataTransformer,
        private readonly ResourceMetadataCollectionFactoryInterface $collectionFactory
    ) {
    }

    /**
     * @param Operation $operation
     * @param array     $uriVariables
     * @param array     $context
     *
     * @return array<WayPointExport>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $collection = $this->collectionFactory->create($operation->getExtraProperties()['entity'])->getOperation(forceCollection: true);

        /** @var Paginator $paginator */
        $paginator = $this->collectionProvider->provide($collection, $uriVariables, $context);
        /** @var array<WayPoint> $wayPoints */
        $wayPoints = \iterator_to_array($paginator, false);

        return \array_map($this->wayPointExportDataTransformer->transform(...), $wayPoints);
    }
}
