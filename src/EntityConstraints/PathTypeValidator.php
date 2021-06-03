<?php

namespace App\EntityConstraints;

use App\Entity\Path;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PathTypeValidator extends ConstraintValidator
{
    protected string $message = 'The "{{ type }}" path type doesn\'t exists.';

    public function validate($value, Constraint $constraint)
    {
        if (!in_array($value, [Path::TYPE_DYNAMIC, Path::TYPE_ALWAYS_VISIBLE, Path::TYPE_MEDIA])) {
            $this->context->buildViolation($this->message)
                ->setParameter('{{ type }}', $value)
                ->addViolation();
        }
    }
}