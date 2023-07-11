<?php
declare(strict_types=1);

namespace App\Dto\Client;

use App\Serializer\Normalizer\Base64DataUriNormalizer;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

trait ClientRequest
{
    #[Assert\NotNull]
    #[Assert\Length(['min' => 3, 'max' => 200])]
    #[Assert\Type(['type' => 'string'])]
    public string $name;

    #[Assert\NotNull]
    #[Assert\Length(['min' => 3, 'max' => 100])]
    #[Assert\Type(['type' => 'string'])]
    public string $email;

    #[Assert\NotNull]
    #[Assert\Length(['min' => 0, 'max' => 10000])]
    #[Assert\Type(['type' => 'string'])]
    public string $description;

    public ?string $ratingImageFileData = null;

    #[Assert\Length(min: 5, max: 200)]
    public ?string $ratingImageFileName = null;

    #[AppAssert\WayPointImageRequirements]
    public function getDecodedRatingImageData(): ?File
    {
        if (!$this->ratingImageFileData) {
            return null;
        }
        $normalizer = new Base64DataUriNormalizer();

        return $normalizer->denormalize($this->ratingImageFileData);
    }
}
