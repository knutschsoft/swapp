<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ContactsCount extends Constraint
{
    public string $messageShouldBeSet = 'way_point.contacts_count_not_set';
    public string $messageShouldNotBeSet = 'way_point.contacts_count_set';

    public function validatedBy(): string
    {
        return ContactsCountValidator::class;
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
