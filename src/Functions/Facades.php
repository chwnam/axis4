<?php

use Changwoo\Axis\Layouts\LayoutFactory;

function axis(): LayoutFactory
{
    static $factory = null;

    if (is_null($factory)) {
        $factory = new LayoutFactory();
    }

    return $factory;
}
