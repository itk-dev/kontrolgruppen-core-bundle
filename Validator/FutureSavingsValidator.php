<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class FutureSavingsValidator.
 */
class FutureSavingsValidator extends ConstraintValidator
{
    /**
     * @param mixed      $serviceEconomyEntry
     * @param Constraint $constraint
     */
    public function validate($serviceEconomyEntry, Constraint $constraint)
    {
        if (!$constraint instanceof FutureSavings) {
            throw new UnexpectedTypeException($constraint, FutureSavings::class);
        }

        if (!empty($serviceEconomyEntry->getFutureSavingsAmount()) && (empty($serviceEconomyEntry->getFutureSavingsPeriodFrom()) || empty($serviceEconomyEntry->getFutureSavingsPeriodTo()))) {
            $this->context->buildViolation('FutureSavingsPeriodFrom cannot be empty.')
                ->addViolation();

            $this->context->buildViolation('FutureSavingsPeriodTo cannot be empty.')
                ->addViolation();
        }

        if ((!empty($serviceEconomyEntry->getFutureSavingsPeriodFrom()) || !empty($serviceEconomyEntry->getFutureSavingsPeriodTo())) && empty($serviceEconomyEntry->getFutureSavingsAmount())) {
            $this->context->buildViolation('FutureSavingsAmount cannot be empty.')
                ->addViolation();
        }
    }
}
