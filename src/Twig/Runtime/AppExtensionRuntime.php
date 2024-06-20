<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    /**
     * Get a price from database (1999 cents)
     *
     * Return value (with ou without taxes) in real unit => 19.99
     */
    public function price(int $value, int $rate = 0): float
    {
        return round($value / 100 * (1 + $rate / 100), 2);
    }
}
