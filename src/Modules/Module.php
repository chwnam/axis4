<?php


namespace Changwoo\Axis\Modules;


use Changwoo\Axis\AbstractLayout;

abstract class Module
{
    private AbstractLayout $layout;

    public function __construct(AbstractLayout $layout)
    {
        $this->layout = $layout;
    }

    abstract public function init();

    public function getLayout(): AbstractLayout
    {
        return $this->layout;
    }
}
