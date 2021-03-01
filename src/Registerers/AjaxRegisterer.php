<?php


namespace Naran\Axis\Registerers;

use Naran\Axis\Registrables\Ajax;
use Closure;


class AjaxRegisterer implements Registerer
{
    private Closure $getObjects;

    public function __construct(callable $getObjects)
    {
        $this->getObjects = Closure::fromCallable($getObjects);

        add_action('init', [$this, 'registerItems']);
    }


    public function registerItems()
    {
        foreach (call_user_func($this->getObjects) as $item) {
            if($item instanceof Ajax) {
                $item->register();
            }
        }

        remove_action('init', [$this, 'registerItems']);
    }
}
