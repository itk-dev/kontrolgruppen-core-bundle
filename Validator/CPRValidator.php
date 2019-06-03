<?php

namespace Kontrolgruppen\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CPRValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \Kontrolgruppen\CoreBundle\Validator\CPR */

        if (null === $value || '' === $value) {
            return;
        }

        // @TODO: Validate according to CPR rules.
        if (!preg_match('/^\d{6}\-\d{4}$/', $value, $matches)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
