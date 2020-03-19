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

    /**
     * ProcessLogTranslatorService constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $namespacedObjectClass
     *
     * @return string
     */
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

    /**
     * @param string $action
     *
     * @return string
     */
    public function translateAction(string $action): string
    {
        return $this->translator->trans(
            'process_log.revision.action_type.'.strtolower($action)
        );
    }

    /**
     * @param string $key
     * @param string $namespacedObjectClass
     *
     * @return string
     */
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

    /**
     * @param $namespacedObjectClass
     *
     * @return string
     */
    protected function extractObjectClassFromNamespace($namespacedObjectClass): string
    {
        $explodedObjectClass = explode('\\', $namespacedObjectClass);

        return end($explodedObjectClass);
    }

    /**
     * @param string $camelCaseString
     *
     * @return string
     */
    protected function camelCaseToUnderscore(string $camelCaseString): string
    {
        $result = strtolower(
            preg_replace('/([a-z])([A-Z])/', '$1_$2', $camelCaseString)
        );

        return $result;
    }
}
