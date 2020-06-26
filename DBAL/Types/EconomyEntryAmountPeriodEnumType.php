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
 * Class EconomyEntryAmountPeriodEnumType.
 */
final class EconomyEntryAmountPeriodEnumType extends AbstractEnumType
{
    public const PR_WEEK = 52;
    public const PR_MONTH = 1;
    public const PR_3_MONTH = 3;
    public const PR_6_MONTH = 6;
    public const PR_YEAR = 12;

    public const TRANSLATIONS = [
        self::PR_WEEK => 'common.enum.economy_entry_amount_period.pr_week',
        self::PR_MONTH => 'common.enum.economy_entry_amount_period.pr_month',
        self::PR_3_MONTH => 'common.enum.economy_entry_amount_period.pr_3_month',
        self::PR_6_MONTH => 'common.enum.economy_entry_amount_period.pr_6_month',
        self::PR_YEAR => 'common.enum.economy_entry_amount_period.pr_year',
    ];

    protected static $choices = self::TRANSLATIONS;
}
