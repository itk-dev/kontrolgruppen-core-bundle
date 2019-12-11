<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

class Cpr
{
    private $cpr;

    public function __construct(string $cpr)
    {
        $this->setCpr($cpr);
    }

    private function setCpr(string $cpr)
    {
        if (strpos($cpr, '-')) {
            $cpr = str_replace('-', '', $cpr);
        }

        if (!filter_var($cpr, FILTER_VALIDATE_INT)) {
            throw new \InvalidArgumentException('$cpr can only contain numbers');
        }

        if ((int) $cpr < 0) {
            throw new \InvalidArgumentException('$cpr cannot be negative');
        }

        if (10 !== \strlen($cpr)) {
            throw new \InvalidArgumentException('$cpr must contain exactly 10 digits');
        }

        $this->cpr = $cpr;
    }

    public function __toString()
    {
        return $this->cpr;
    }
}
