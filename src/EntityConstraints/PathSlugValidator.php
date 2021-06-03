<?php

namespace App\EntityConstraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PathSlugValidator extends ConstraintValidator
{
    protected string $message = 'The "{{ path }}" {{ format }} path contains invalid characters.';

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PathSlugConstraint) {
            throw new UnexpectedTypeException($constraint, PathSlugConstraint::class);
        }
        $regex = $constraint->getFormat() == PathSlugConstraint::FORMAT_COMPLETE
            ? '#^[a-z.\d/_-]*[a-z.\d_-]$#' // This format allows slashes, but not at the end.
            : '#^[a-z.\d_-]*$#';
        if (!preg_match($regex, $value)) {
            $this->context->buildViolation($this->message)
                ->setParameter('{{ path }}', $value)
                ->setParameter('{{ format }}', $constraint->getFormat())
                ->addViolation();
        }
    }
}