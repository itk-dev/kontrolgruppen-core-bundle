<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Validator;

use Kontrolgruppen\CoreBundle\Entity\ServiceEconomyEntry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class RepaymentValidator.
 */
class RepaymentValidator extends ConstraintValidator
{
    /**
     * @param mixed      $serviceEconomyEntry
     * @param Constraint $constraint
     */
    public function validate($serviceEconomyEntry, Constraint $constraint)
    {
        /* @var ServiceEconomyEntry $serviceEconomyEntry */

        if (!$constraint instanceof Repayment) {
            throw new UnexpectedTypeException($constraint, Repayment::class);
        }

        if (!empty($serviceEconomyEntry->getRepaymentAmount()) && (empty($serviceEconomyEntry->getRepaymentPeriodFrom()) || empty($serviceEconomyEntry->getRepaymentPeriodTo()))) {
            $this->context->buildViolation('RepaymentPeriodFrom cannot be empty.')
                ->addViolation();

            $this->context->buildViolation('RepaymentPeriodTo cannot be empty.')
                ->addViolation();
        }

        if ((!empty($serviceEconomyEntry->getRepaymentPeriodFrom()) || !empty($serviceEconomyEntry->getRepaymentPeriodTo())) && empty($serviceEconomyEntry->getRepaymentAmount())) {
            $this->context->buildViolation('RepaymentAmount cannot be empty.')
                ->addViolation();
        }
    }
}
