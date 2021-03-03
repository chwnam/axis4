<?php

namespace Naran\Axis\Layouts;

use Naran\Axis\Container;
use Naran\Axis\Interfaces\Layout;
use Naran\Axis\Modules\Module;
use Naran\Axis\Registerers\AjaxRegisterer;
use Naran\Axis\Registrables\Registrable;
use Naran\Axis\Renderers\EjsRenderer;
use Naran\Axis\Renderers\FileRenderer;

abstract class BaseLayout implements Layout
{
    protected array $modules = [];

    protected array $conditionalModules = [];

    protected array $registrables = [];

    protected Container $container;

    /**
     * Version
     *
     * @var string
     */
    private string $version = '';

    /**
     * Slug
     *
     * A unique identifier for this layout.
     *
     * @var string
     */
    private string $slug = '';

    /**
     * Textdomain
     *
     * @var string
     */
    private string $textdomain = '';

    /**
     * Default action and filter priority.
     *
     * @var int
     */
    private int $priority = 10;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get(string $name)
    {
        if ($this->container->isAlias($name)) {
            return $this->container->get($name);
        }
        return null;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return $this
     */
    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getTextdomain(): string
    {
        return $this->textdomain;
    }

    /**
     * @param string $textdomain
     *
     * @return $this
     */
    public function setTextdomain(string $textdomain): self
    {
        $this->textdomain = $textdomain;

        return $this;
    }

    public function getDefaultPriority(): int
    {
        return $this->priority;
    }

    public function setDefaultPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function includeFile(string $relpath): self
    {
        $__inc__ = trailingslashit(dirname($this->getMainFile())) . $relpath;

        if (file_exists($__inc__) && is_readable($__inc__)) {
            (function () use ($__inc__) {
                /** @noinspection PhpIncludeInspection */
                include $__inc__;
            })();
        }

        return $this;
    }

    public function setRootModules(array $modules): self
    {
        $this->modules = $modules;

        foreach ($this->modules as $name => $module) {
            $class = $this->isConditionalModule($module) ? $module[0] : $module;
            $this->container->bindIf($class, null, true);
            if ( ! is_numeric($name) && ($name = sanitize_key($name))) {
                $this->container->alias($class, $name);
            }
        }

        return $this;
    }

    public function addRegistrables(string $type, callable $registrables): self
    {
        $this->registrables[$type] = $registrables;

        return $this;
    }

    public function activationSetup()
    {
        do_action('axis_activation', $this->getSlug());
    }

    public function deactivationCleanup()
    {
        do_action('axis_deactivation', $this->getSlug());
    }

    public function initConditionalModules()
    {
        foreach ($this->conditionalModules as $name => $module) {
            if (($dynamic = $this->initContiaionalModule($module))) {
                $this->modules[$name] = $dynamic;
            }
        }

        $this->conditionalModules = [];
    }

    public function standby()
    {
        if (empty($this->getMainFile())) {
            wp_die(
                esc_html__('Main file is missing. Please call setMainFile().', 'axis'),
                esc_html__('Axis setup failure', 'axis')
            );
        }

        if (empty($this->getSlug())) {
            wp_die(
                esc_html__('Slug string is missing. Please call setSlug().', 'axis'),
                esc_html__('Axis setup failure', 'axis')
            );
        }

        $this->defaultBindings();
        $this->addDefaultHooks();
        $this->initRootModules();
        $this->initRegisterers();

        do_action('axis_stanby', $this->getSlug());
    }

    protected function initRootModules()
    {
        foreach ($this->modules as $name => $module) {
            if (($regular = $this->initRegularModule($module))) {
                $this->modules[$name] = $regular;
            } elseif ($this->isConditionalModule($module)) {
                $this->conditionalModules[$name] = $module;
            }
        }

        if ( ! empty($this->conditionalModules)) {
            add_action('wp', [$this, 'initConditionalModules'], $this->getDefaultPriority());
        }
    }

    protected function initRegisterers()
    {
        $classmap = [
            Registrable::AJAX => AjaxRegisterer::class,
        ];

        foreach ($this->registrables as $type => $getObjects) {
            if (isset($classmap[$type]) && class_exists($classmap[$type])) {
                new $classmap[$type]($getObjects);
            }
        }

        unset($this->registrables);
    }

    /**
     * @param $module
     *
     * @return Module|object|null
     */
    protected function initRegularModule(&$module)
    {
        return is_string($module) ? $this->initRootModule($module) : null;
    }

    /**
     * @param $module
     *
     * @return bool
     */
    protected function isConditionalModule(&$module): bool
    {
        return is_array($module) && 2 === count($module) && is_callable($module[1]);
    }

    /**
     * @param $module
     *
     * @return Module|object|null
     */
    protected function initContiaionalModule(&$module)
    {
        return $this->isConditionalModule($module) && $module[1]() ? $this->initRootModule($module[0]) : null;
    }

    /**
     * @param string $module
     *
     * @return mixed
     */
    protected function initRootModule(string $module)
    {
        return $this->container->get($module);
    }

    protected function defaultBindings()
    {
        $this->container->singletonIf(FileRenderer::class);
        $this->container->singletonIf(EjsRenderer::class);
    }
}
