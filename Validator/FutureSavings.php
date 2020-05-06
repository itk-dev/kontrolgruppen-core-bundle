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

/**
 * @Annotation
 */
class FutureSavings extends Constraint
{
    public $message = '';

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * @return string
     */
    public function validatedBy()
    {
        return FutureSavingsValidator::class;
    }
}
