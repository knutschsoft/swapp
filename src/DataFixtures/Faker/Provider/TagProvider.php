<?php
declare(strict_types=1);

namespace App\DataFixtures\Faker\Provider;

class TagProvider
{
    private static $position = 0;
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

    public static function tagName()
    {
        $tagName = self::$tagNames[self::$position];

        self::$position++;

        if (self::$position >= \count(self::$tagNames)) {
            self::$position = 0;
        }

        return $tagName;
    }
}
