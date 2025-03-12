<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            new TwigFilter('substr', [$this, 'substr']),
        ];
    }

    public function substr($string)
    {
        return substr($string, 0, 125).'...';
    }
}