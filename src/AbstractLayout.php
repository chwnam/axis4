<?php


namespace Changwoo\Axis;


use Changwoo\Axis\Modules\Module;
use Changwoo\Axis\Registerers\RegistererFactory;


abstract class AbstractLayout
{
    /**
     * Created layouts
     *
     * @var AbstractLayout[]
     */
    private static array $pool = [];

    /**
     * Root-level modules.
     *
     * Basically, subclasses of Module class, but anything can be loaded.
     *
     * @var array
     */
    protected array $modules = [];

    /**
     * All registrables.
     *
     * Each callable returns an array of registrable.
     *
     * @var array<string, callable>
     */
    protected array $registrables = [];

    /**
     * Contextual modules.
     *
     * Each module is instantiated under a specific context.
     *
     * @var array
     */
    private array $contextualModules = [];

    /**
     * Main file.
     *
     * @var string
     */
    private string $mainFile = '';

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

    /**
     * Get layout from the pool
     *
     * @param string $slug
     *
     * @return AbstractLayout|null
     */
    public static function getLayoutFromPool(string $slug): ?AbstractLayout
    {
        return self::$pool[$slug] ?? null;
    }

    /**
     * Dynamic access to root-level modules.
     *
     * @param string $name
     *
     * @return Module|mixed|false
     */
    public function __get(string $name)
    {
        $module = false;

        if ( ! is_numeric($name) && isset($this->modules[$name])) {
            $module = $this->modules[$name];
            if (is_callable($module)) {
                // capsuled module
                $this->modules[$name] = $module = call_user_func($module);
                if ($module instanceof Module) {
                    $module->init();
                }
            } elseif ($this->isContextualModule($module)) {
                // contextual module
                $this->modules[$name] = $module = call_user_func($module[0]);
                if ($module instanceof Module) {
                    $module->init();
                }
            }
        }

        return $module;
    }

    /**
     * Tell if module is contextual.
     *
     * @param mixed $module An array length 2, all callable items is true.
     *                      0th item: module generator function.
     *                      1st item: contextual test function.
     *
     * @return bool
     *
     */
    protected function isContextualModule(&$module): bool
    {
        return is_array($module) && 2 === count($module) && is_callable($module[0]) && is_callable($module[1]);
    }

    /**
     * Helper for creating dynamic modules
     *
     * @param string|object $class
     *
     * @return callable|null
     */
    public function wrapModule($class): ?callable
    {
        if (class_exists($class)) {
            $self = &$this;

            return function () use ($self, $class) {
                if (is_string($class)) {
                    if (is_subclass_of($class, Module::class, true)) {
                        return new $class($self);
                    } elseif (class_exists($class)) {
                        return new $class();
                    } else {
                        return null;
                    }
                }
                return $class;
            };
        }

        return null;
    }

    /**
     * Setter for root-level modules.
     *
     * Each array item can be:
     * - Anonymous module: value with a numeric index. Cannot be queried.
     * - Named module: value with a unique string key. Use __get() method with the key to get this module.
     * - Dynamic module: Each anonymous, named module is always instantiated when layout calls stanby(). But this module is instantiated just when it is called.
     * - Contextual module: Like dynamic module, it is dynamically instantiaed. But you can assign a condition that makes this module to be instantiated.
     *
     * @param array $modules
     *
     * @return $this
     *
     * @sample $this->setModules(
     *              [
     *                  new SomeModule($this),                                          // Anonymous module.
     *                  'foo' => new FooModule($this),                                  // Named. $this->foo is okay.
     *                  'bar' => $thing->wrapModule(BarModule::class),                  // Dynamic. $this->bar is okay, but instantiated just when the first __get() invoke.
     *                  'baz' => [$thing->wrapModule(BazModule::class), 'is_archive'],  // Contextual. Instantiated explicit $this->baz is met, or implicitly when is_archive() is true.
     *              ]
     *         );
     */
    public function setModules(array $modules): self
    {
        $this->modules = $modules;

        return $this;
    }

    public function addRegistrables(string $type, callable $registrables): self
    {
        $this->registrables[$type] = $registrables;

        return $this;
    }

    public function loadRegisterFile(string $relpath): self
    {
        $__inc__ = trailingslashit(dirname($this->getMainFile())) . $relpath . '.php';
        if (file_exists($__inc__) && is_readable($__inc__)) {
            (function () use ($__inc__) {
                /** @noinspection PhpIncludeInspection */
                include $__inc__;
            })();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getMainFile(): string
    {
        return $this->mainFile;
    }

    /**
     * @param string $mainFile
     *
     * @return $this
     */
    public function setMainFile(string $mainFile): self
    {
        $this->mainFile = $mainFile;

        return $this;
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

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $prority)
    {
        $this->priority = $prority;
    }

    public function activationSetup()
    {
        do_action('axis_activation', $this->getSlug());
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function deactivationCleanup()
    {
        do_action('axis_deactivation', $this->getSlug());
    }

    public function standby()
    {
        $mainFile = $this->getMainFile();
        if (empty($mainFile)) {
            wp_die(
                esc_html__('Main file is missing. Please call setMainFile().', 'axis'),
                esc_html__('Axis setup failure', 'axis')
            );
        } elseif ( ! file_exists($mainFile) || ! is_file($mainFile)) {
            wp_die(
                esc_html__('Main file is not a valid file.', 'axis'),
                esc_html__('Axis setup failure', 'axis')
            );
        }

        $slug = $this->getSlug();
        if (empty($slug)) {
            $this->setSlug(pathinfo($mainFile, PATHINFO_FILENAME));
        }

        self::$pool[$this->getSlug()] = $this;
        $this->addDefaultHooks();

        // load all modules
        $this->initModules();
        add_action('wp', [$this, 'initContexturalModules'], 5);

        // init all registrables
        RegistererFactory::factory($this->registrables);
        unset($this->registrables);

        do_action('axis_stanby', $slug);
    }

    abstract protected function addDefaultHooks();

    protected function initModules()
    {
        foreach ($this->modules as $name => $module) {
            if ( ! is_callable($module)) {
                if ($module instanceof Module) {
                    $module->init();
                } elseif ($this->isContextualModule($module)) {
                    $this->contextualModules[$name] = $module;
                }
            }
        }
    }

    /**
     * Initialize contextual modules, if they are available.
     *
     * @callback
     * @action      wp
     */
    public function initContexturalModules()
    {
        foreach ($this->contextualModules as $name => $module) {
            if ($this->isContextualModule($module) && call_user_func($module[1])) {
                $this->modules[$name] = $module = call_user_func($module[0]);
                if ($module instanceof Module) {
                    $module->init();
                }
            }
        }

        $this->contextualModules = [];
    }
}
