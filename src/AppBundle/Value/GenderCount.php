<?php

namespace AppBundle\Value;


final class GenderCount
{
    /** @var int */
    private $females;
    /** @var int */
    private $males;
    /** @var int */
    private $undefined;

    private function __construct($female = 0, $male = 0, $undefined = 0)
    {
        $this->setFemales($female);
        $this->setMales($male);
        $this->setUndefined($undefined);
    }

    public static function forMale($male)
    {
        return new self(0, $male);
    }

    public static function forFemale($female)
    {
        return new self($female);
    }

    public static function forUndefined($undefined)
    {
        return new self(0, 0, $undefined);
    }

    public static function fromArray(array $count)
    {
        $female = isset($count['female']) ? $count['female'] : isset($count[0]) ? $count[0] : 0;
        $male = isset($count['male']) ? $count['male'] : isset($count[1]) ? $count[1] : 0;
        $undefined = isset($count['undefined']) ? $count['undefined'] : isset($count[2]) ? $count[2] : 0;

        return new self($female, $male, $undefined);
    }

    public function increase(GenderCount $count)
    {
        return new self(
            $this->females + $count->females(),
            $this->males + $count->males(),
            $this->undefined + $count->undefined()
        );
    }

    public function decrease(GenderCount $count)
    {
        $female = $this->females - $count->females();
        $male = $this->males - $count->males();
        $undefined = $this->undefined - $count->undefined();

        return new self(
            $female > 0 ? $female : 0,
            $male > 0 ? $male : 0,
            $undefined > 0 ? $undefined : 0
        );
    }

    public function females()
    {
        return $this->females;
    }

    public function males()
    {
        return $this->males;
    }

    public function undefined()
    {
        return $this->undefined;
    }

    private function setFemales($count)
    {
        if (!$this->isPositiveInt($count)) {
            throw new \InvalidArgumentException('Count must be a positive Integer.');
        }

        $this->females = $count;
    }

    private function setMales($count)
    {
        if (!$this->isPositiveInt($count)) {
            throw new \InvalidArgumentException('Count must be a positive integer.');
        }

        $this->males = $count;
    }

    private function setUndefined($undefined)
    {
        if (!$this->isPositiveInt($undefined)) {
            throw new \InvalidArgumentException('Count must be a positive integer.');
        }

        $this->undefined = $undefined;

    }

    private function isPositiveInt($number)
    {
        return is_int($number) && (int)$number >= 0;
    }

}
