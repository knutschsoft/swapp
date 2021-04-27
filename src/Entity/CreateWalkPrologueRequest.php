<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     messenger=true,
 *     collectionOperations={
 *         "post"={"status"=201}
 *     },
 *     itemOperations={}
 * )
 */
final class CreateWalkPrologueRequest
{
    /**
     * @var int
     *
     * @Assert\NotBlank
     */
    public $teamId;
}
