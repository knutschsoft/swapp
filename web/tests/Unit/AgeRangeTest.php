<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Value\AgeRange;
use PHPUnit\Framework\TestCase;

class AgeRangeTest extends TestCase
{
    private const int START = 3;

    private const int END = 7;

    /**
     * @param array $count
     *
     * @dataProvider fromArrayProvider
     */
    public function test_from_array(array $count): void
    {
        $ageRange = AgeRange::fromArray($count);

        $this->assertEquals($ageRange->getRangeStart(), self::START);
        $this->assertEquals($ageRange->getRangeEnd(), self::END);
    }

    public function fromArrayProvider(): array
    {
        return [
            [[self::START, self::END]],
            [[0 => self::START, 1 => self::END]],
            [[1 => self::END, 0 => self::START]],
            [['start' => self::START, 'end' => self::END]],
            [['end' => self::END, 'start' => self::START]],
        ];
    }
}
