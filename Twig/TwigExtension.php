<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('iconClass', [$this, 'getIconClass']),
        ];
    }

    public function getIconClass(string $name)
    {
        switch ($name) {
            case 'dashboard':
                return 'fa-tachometer-alt';
            case 'process':
                return 'fa-tasks';
            case 'profile':
                return 'fa-id-card';
            case 'users':
                return 'fa-users-cog';
            case 'admin':
                return 'fa-cog';
            case 'reminder':
                return 'fa-clock';
            case 'not-assigned':
                return 'fa-user-plus';
            case 'not-visited':
                return 'fa-archive';
            case 'show':
                return 'fa-eye';
            case 'edit':
                return 'fa-pencil-alt';

            default:
                return '';
        }
    }
}
