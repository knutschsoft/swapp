<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Service\UserPreferencesSchema;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidUserPreferencesValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidUserPreferences) {
            throw new UnexpectedTypeException($constraint, ValidUserPreferences::class);
        }

        if (null === $value || [] === $value) {
            return;
        }

        if (!\is_array($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }

        // Validate top-level structure
        if (!isset($value['tables']) || !\is_array($value['tables'])) {
            // If preferences don't contain 'tables', they might be empty or malformed
            // We allow empty preferences (user hasn't customized anything yet)
            return;
        }

        $validTables = UserPreferencesSchema::getValidTables();

        foreach ($value['tables'] as $tableName => $tablePrefs) {
            // Validate table name
            if (!\in_array($tableName, $validTables, true)) {
                $this->context->buildViolation($constraint->invalidTableMessage)
                    ->setParameter('{{ table }}', $tableName)
                    ->addViolation();
                continue;
            }

            if (!\is_array($tablePrefs)) {
                continue;
            }

            // Validate columns
            if (isset($tablePrefs['columns']) && \is_array($tablePrefs['columns'])) {
                $validColumns = UserPreferencesSchema::getValidColumnsForTable($tableName);

                foreach ($tablePrefs['columns'] as $columnName => $columnValue) {
                    if (!\in_array($columnName, $validColumns, true)) {
                        $this->context->buildViolation($constraint->invalidColumnMessage)
                            ->setParameter('{{ column }}', $columnName)
                            ->setParameter('{{ table }}', $tableName)
                            ->addViolation();
                        continue;
                    }

                    if (!\is_bool($columnValue)) {
                        $this->context->buildViolation($constraint->invalidTypeMessage)
                            ->setParameter('{{ key }}', "tables.$tableName.columns.$columnName")
                            ->setParameter('{{ expectedType }}', 'bool')
                            ->setParameter('{{ actualType }}', \get_debug_type($columnValue))
                            ->addViolation();
                    }
                }
            }

            // Validate filters
            if (isset($tablePrefs['filters']) && \is_array($tablePrefs['filters'])) {
                $validFilters = UserPreferencesSchema::getValidFiltersForTable($tableName);

                foreach ($tablePrefs['filters'] as $filterName => $filterValue) {
                    if (!\in_array($filterName, $validFilters, true)) {
                        $this->context->buildViolation($constraint->invalidFilterMessage)
                            ->setParameter('{{ filter }}', $filterName)
                            ->setParameter('{{ table }}', $tableName)
                            ->addViolation();
                        continue;
                    }

                    if (!\is_bool($filterValue)) {
                        $this->context->buildViolation($constraint->invalidTypeMessage)
                            ->setParameter('{{ key }}', "tables.$tableName.filters.$filterName")
                            ->setParameter('{{ expectedType }}', 'bool')
                            ->setParameter('{{ actualType }}', \get_debug_type($filterValue))
                            ->addViolation();
                    }
                }
            }
        }
    }
}
