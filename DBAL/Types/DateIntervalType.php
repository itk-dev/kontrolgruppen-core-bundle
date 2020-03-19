<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Class DateIntervalType.
 */
final class DateIntervalType extends AbstractEnumType
{
    public const THIS_WEEK = 'THIS_WEEK';
    public const WEEK = 'WEEK';
    public const TWO_WEEKS = 'TWO_WEEKS';
    public const MONTH = 'MONTH';

    protected static $choices = [
        self::THIS_WEEK => 'common.enum.date_interval.this_week',
        self::WEEK => 'common.enum.date_interval.week',
        self::TWO_WEEKS => 'common.enum.date_interval.two_weeks',
        self::MONTH => 'common.enum.date_interval.month',
    ];
}
