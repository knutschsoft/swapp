<?php
declare(strict_types=1);

namespace App\Dto;

use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TagCreateRequest
 *
 * @Assert\GroupSequence({"TagCreateRequest", "Second"})
 */
final class TagCreateRequest
{
    /**
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Length(min="3", max="100", normalizer="trim")
     * @Assert\Type(type="string")
     *
     * @AppAssert\IsTagNameUnique(groups="Second")
     */
    //#[Assert\NotBlank]
    //#[Assert\NotNull]
    //#[Assert\Length(['min' => 3, 'max' => 100])]
    //#[Assert\Type(['type' => 'string'])]
    //#[AppAssert\IsTagNameUnique()]
    public string $name;
    /**
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Type(type="string")
     * @Assert\Choice(choices=\App\Entity\Tag::COLORS)
     *
     * @AppAssert\IsTagColorUnique(groups="Second")
     */
    //#[AppAssert\IsTagColorUnique()]
    public string $color;
}
