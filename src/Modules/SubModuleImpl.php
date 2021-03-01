<?php


namespace Naran\Axis\Modules;


trait SubModuleImpl
{
    protected array $modules = [];

    public function __get(string $name): ?Module
    {
        return $this->modules[$name] ?? null;
    }

    protected function initSubModules(array $modules)
    {
        $container = $this->getContainer();
        foreach ($modules as $name => $module) {
            $container->singletonIf($module);
            $this->modules[$name] = $container->get($module);
        }
    }
}
