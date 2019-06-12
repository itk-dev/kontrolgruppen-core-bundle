<?php


namespace Kontrolgruppen\CoreBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class ProcessLogEntryLevelEnumType extends AbstractEnumType
{
    public const INFO = 'INFO';
    public const NOTICE = 'NOTICE';

    protected static $choices = [
        self::INFO => 'INFO',
        self::NOTICE => 'NOTICE',
    ];
}
