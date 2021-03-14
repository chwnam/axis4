<?php


namespace Naran\Axis\Modules;


use Closure;

trait SubModuleImpl
{
    protected array $modules = [];

    public function __get(string $name)
    {
        $module = $this->modules[$name] ?? null;

        if ($module instanceof Closure) {
            $module = call_user_func($module, $this);
            if (is_string($module) && class_exists($module)) {
                $container = $this->getContainer();
                $container->singletonIf($module);
                $this->modules[$name] = $module = $container->get($module);
            } else {
                $this->modules[$name] = $module;
            }
        }

        return $module;
    }

    protected function initSubModules(array $modules)
    {
        $container = $this->getContainer();

        foreach ($modules as $name => $module) {
            if (is_string($module)) {
                $container->singletonIf($module);
                $this->modules[$name] = $container->get($module);
            } else {
                $this->modules[$name] = $module;
            }
        }
    }
}
