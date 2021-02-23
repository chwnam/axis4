<?php


namespace Changwoo\Axis\Registerers;


use Changwoo\Axis\Registrables\Registrable;

class RegistererFactory
{
    public static function factory(array &$registrables)
    {
        $classmap = [
            Registrable::AJAX => AjaxRegisterer::class,
        ];

        foreach ($registrables as $type => $getObjects) {
            if (isset($classmap[$type]) && class_exists($classmap[$type])) {
                new $classmap[$type]($getObjects);
            } elseif ( ! empty($type)) {
                $defaultClass = apply_filters("axis_default_registerer_class_{$type}", null);
                if ($defaultClass && class_exists($defaultClass)) {
                    new $defaultClass($getObjects);
                }
            }
        }
    }
}
