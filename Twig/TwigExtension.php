<?php

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
            case 'dashboard': return 'fa-tachometer-alt';
            case 'process': return 'fa-tasks';
            case 'profile': return 'fa-id-card';
            case 'users': return 'fa-users-cog';
            case 'admin': return 'fa-cog';
            case 'reminder': return 'fa-clock';
            case 'not-assigned': return 'fa-user-plus';
            case 'not-visited': return 'fa-archive';
            case 'tasks': return 'fa-tasks';

            default: return '';
        }
    }
}
