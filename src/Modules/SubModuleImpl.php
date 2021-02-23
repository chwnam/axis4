<?php


namespace Changwoo\Axis\Modules;


trait SubModuleImpl
{
    protected array $modules = [];

    public function initModules(array $modules)
    {
        $this->modules = $modules;

        foreach ($this->modules as $module) {
            if ($module instanceof Module) {
                $module->init();
            }
        }
    }

    public function __get(string $name): ?Module
    {
        $module = $this->modules[$name] ?? null;

        if (is_callable($module)) {
            $module = call_user_func($module);
            if ($module instanceof Module) {
                $module->init();
            }
            if ($module) {
                $this->modules[$name] = $module;
            }
        }

        return $module;
    }
}
