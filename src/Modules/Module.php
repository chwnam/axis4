<?php


namespace Changwoo\Axis\Modules;

use Changwoo\Axis\Container;
use Changwoo\Axis\Interfaces\Layout;

abstract class Module
{
    private Layout $layout;

    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    public function getLayout(): Layout
    {
        return $this->layout;
    }

    public function getContainer(): Container
    {
        return $this->layout->getContainer();
    }
}
