<?php

namespace App\EntityConstraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\InvalidOptionsException;

/**
 * @Annotation
 */
class PathSlugConstraint extends Constraint
{
    public const FORMAT_COMPLETE = 'complete';
    public const FORMAT_PARTIAL = 'partial';

    protected string $format;

    public function getDefaultOption()
    {
        return 'format';
    }

    public function getFormat() : string
    {
        if (!in_array($this->format, [self::FORMAT_COMPLETE, self::FORMAT_PARTIAL])) {
            throw new InvalidOptionsException('The "format" option has an invalid value.', []);
        }
        return $this->format;
    }

    public function validatedBy()
    {
        return PathSlugValidator::class;
    }
}