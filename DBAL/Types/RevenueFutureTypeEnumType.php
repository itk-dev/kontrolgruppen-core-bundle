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
 * Class RevenueFutureTypeEnumType.
 */
final class RevenueFutureTypeEnumType extends AbstractEnumType
{
    public const FIXED_VALUE = 'FIXED_VALUE';
    public const SANCTION = 'SANCTION';
    public const PR_MND_X_12 = 'PR_MND_X_12';
    public const SELF_SUPPORT = 'SELF_SUPPORT';
    public const PR_WEEK_X_52 = 'PR_WEEK_X_52';

    public const TRANSLATIONS = [
        self::FIXED_VALUE => 'common.enum.revenue_future_type.fixed_value',
        self::SANCTION => 'common.enum.revenue_future_type.sanction',
        self::PR_MND_X_12 => 'common.enum.revenue_future_type.pr_mnd_x_12',
        self::SELF_SUPPORT => 'common.enum.revenue_future_type.self_support',
        self::PR_WEEK_X_52 => 'common.enum.revenue_future_type.pr_week_x_52',
    ];

    protected static $choices = self::TRANSLATIONS;
}
