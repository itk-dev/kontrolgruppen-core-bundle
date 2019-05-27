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

final class JournalEntryEnumType extends AbstractEnumType
{
    public const NOTE = 'NOTE';
    public const INTERNAL_NOTE = 'INTERNAL_NOTE';

    protected static $choices = [
        self::NOTE => 'common.enum.journal_entry.note',
        self::INTERNAL_NOTE => 'common.enum.journal_entry.internal_note',
    ];
}
