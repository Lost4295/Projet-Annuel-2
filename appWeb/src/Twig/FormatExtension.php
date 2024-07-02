<?php
namespace App\Twig;

use App\Controller\DevisController;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormatExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('di', [$this, 'format']),
        ];
    }

    public function format(string $di): string
    {
        return DevisController::dateIntervalToHumanString(new \DateInterval($di));
    }
}