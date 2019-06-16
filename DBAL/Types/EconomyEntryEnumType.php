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

final class EconomyEntryEnumType extends AbstractEnumType
{
    public const SERVICE = 'SERVICE';
    public const ACCOUNT = 'ACCOUNT';
    public const ARREAR = 'ARREAR';

    public const TRANSLATIONS = [
        self::SERVICE => 'common.enum.economy_entry.service',
        self::ACCOUNT => 'common.enum.economy_entry.account',
        self::ARREAR => 'common.enum.economy_entry.arrear',
    ];

    protected static $choices = self::TRANSLATIONS;
}
