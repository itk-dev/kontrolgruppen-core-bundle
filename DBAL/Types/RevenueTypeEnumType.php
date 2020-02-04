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
 * Class RevenueTypeEnumType.
 */
final class RevenueTypeEnumType extends AbstractEnumType
{
    public const FUTURE_SAVINGS = 'FUTURE_SAVINGS';
    public const REPAYMENT = 'REPAYMENT';

    public const TRANSLATIONS = [
        self::FUTURE_SAVINGS => 'common.enum.revenue_type.future_savings',
        self::REPAYMENT => 'common.enum.revenue_type.repayment',
    ];

    protected static $choices = self::TRANSLATIONS;
}
