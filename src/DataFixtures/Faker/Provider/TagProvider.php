<?php
declare(strict_types=1);

namespace App\DataFixtures\Faker\Provider;

use Faker\Provider\Base;

class TagProvider extends Base
{
    /** @var int */
    private static $position = 0;
    /** @var string[] */
    private static $tagNames = [
        'Gewalt (körperlich)',
        'Gewalt (verbal)',
        'Drogen',
        'Partnerschaft',
        'Jobcenter',
        'Ausbildung',
        'Sexualität',
        'Politik',
        'Wohnungslosigkeit',
        'Schwangerschaft',
        'Polizei',
        'Sozialstunden',
        'Gemeinwesen',
        'Grundversorgung',
    ];

    public static function tagName(): string
    {
        $tagName = self::$tagNames[self::$position];

        self::$position++;

        if (self::$position >= \count(self::$tagNames)) {
            self::$position = 0;
        }

        return $tagName;
    }
}
