<?php

namespace Naran\Axis\Registerers;

use Naran\Axis\Interfaces\Registerer;
use Naran\Axis\Registrables\Style;


class StyleRegisterer implements Registerer
{
    private $getObjects;

    public function __construct(callable $getObjects, int $proiority = 10)
    {
        $this->getObjects = $getObjects;

        add_action('init', [$this, 'registerItems'], $proiority);
    }

    public function registerItems()
    {
        foreach ($this->getItems() as $item) {
            if ($item instanceof Style) {
                $item->register();
            }
        }
    }

    public function getItems(): array
    {
        return ($this->getObjects)();
    }
}
