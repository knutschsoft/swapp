<?php
declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\LocationName;
use App\Entity\WayPoint;

class LocationNameDataTransformer implements DataTransformerInterface
{
    /**
     * @param WayPoint $data
     * @param string   $to
     * @param array    $context
     *
     * @return LocationName
     */
    public function transform($data, string $to, array $context = []): LocationName
    {
        $locationName = new LocationName();
        $locationName->name = $data->getLocationName();

        return $locationName;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return LocationName::class === $to && $data instanceof WayPoint;
    }
}
