<?php
declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Export\WalkExport;
use App\Entity\Walk;

class WalkExportProvider implements ProviderInterface
{
    public function __construct(
        private readonly CollectionProvider $collectionProvider,
        private readonly WalkExportDataTransformer $walkExportDataTransformer,
        private readonly ResourceMetadataCollectionFactoryInterface $collectionFactory
    ) {
    }

    /**
     * @param Operation $operation
     * @param array     $uriVariables
     * @param array     $context
     *
     * @return array<WalkExport>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $collection = $this->collectionFactory->create($operation->getExtraProperties()['entity'])->getOperation(forceCollection: true);

        /** @var Paginator $paginator */
        $paginator = $this->collectionProvider->provide($collection, $uriVariables, $context);
        /** @var array<Walk> $walks */
        $walks = \iterator_to_array($paginator, false);

        return \array_map($this->walkExportDataTransformer->transform(...), $walks);
    }
}
