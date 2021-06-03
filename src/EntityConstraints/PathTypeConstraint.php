<?php

namespace App\EntityConstraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PathTypeConstraint extends Constraint
{
    public function validatedBy()
    {
        return PathTypeValidator::class;
    }
}