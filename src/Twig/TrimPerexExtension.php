<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TrimPerexExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('trimPerex', [$this, 'TrimPerex']),
        ];
    }

    public function trimPerex($text, $length = 92): string
    {
        if (strlen($text) <= $length) return $text;
        $hardTrim = substr($text, 0, $length);
        $spaceIndex = strrpos($hardTrim, ' ');
        return substr($text, 0, $spaceIndex) . '...';
    }
}
