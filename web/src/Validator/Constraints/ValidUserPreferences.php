<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validates that user preferences conform to the schema defined in UserPreferencesSchema.
 *
 * This ensures:
 * - Only valid table names are used
 * - Only valid column/filter names are used
 * - Values are of correct types (bool for columns/filters)
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class ValidUserPreferences extends Constraint
{
    public string $message = 'Invalid preference structure';
    public string $invalidTableMessage = 'Unknown table "{{ table }}"';
    public string $invalidColumnMessage = 'Unknown column "{{ column }}" for table "{{ table }}"';
    public string $invalidFilterMessage = 'Unknown filter "{{ filter }}" for table "{{ table }}"';
    public string $invalidTypeMessage = 'Value for "{{ key }}" must be of type {{ expectedType }}, {{ actualType }} given';
}
