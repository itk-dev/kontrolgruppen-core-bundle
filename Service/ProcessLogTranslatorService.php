<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ProcessLogTranslatorService.
 */
class ProcessLogTranslatorService
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function translateObjectClass(string $namespacedObjectClass): string
    {
        $objectClass = strtolower(
            $this->camelCaseToUnderscore(
                $this->extractObjectClassFromNamespace($namespacedObjectClass)
            )
        );

        return $this->translator->trans(
            'process_log.revision.table.object_types.'.$objectClass
        );
    }

    public function translateAction(string $action): string
    {
        return $this->translator->trans(
            'process_log.revision.action_type.'.strtolower($action)
        );
    }

    public function translateDataKey(string $key, string $namespacedObjectClass): string
    {
        $objectClass = strtolower(
            $this->camelCaseToUnderscore(
                $this->extractObjectClassFromNamespace($namespacedObjectClass)
            )
        );

        $keyTransKey = 'process_log.revision.table.log_entry.'
                    .$objectClass
                    .'.'
                    .$this->camelCaseToUnderscore($key);

        return $this->translator->trans($keyTransKey);
    }

    protected function extractObjectClassFromNamespace($namespacedObjectClass): string
    {
        $explodedObjectClass = explode('\\', $namespacedObjectClass);

        return end($explodedObjectClass);
    }

    protected function camelCaseToUnderscore(string $camelCaseString): string
    {
        $result = strtolower(
            preg_replace('/([a-z])([A-Z])/', '$1_$2', $camelCaseString)
        );

        return $result;
    }
}
