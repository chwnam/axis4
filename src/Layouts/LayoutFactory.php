<?php


namespace Changwoo\Axis\Layouts;

use Changwoo\Axis\Container;
use Changwoo\Axis\Interfaces\Layout;
use InvalidArgumentException;

final class LayoutFactory
{
    /**
     * @var Layout[]
     */
    private array $layouts = [];

    protected static function getLayoutIdentifier(string $layoutType, string $slug): string
    {
        if (empty($layoutType) || empty($slug)) {
            throw new InvalidArgumentException('$layoutType and $slug cannot be empty.');
        }
        return "axis.{$layoutType}.{$slug}";
    }

    public function makePlugin(string $slug): PluginLayout
    {
        /** @var PluginLayout $layout */
        $layout = $this->makeLayout(PluginLayout::class, 'plugin', $slug);

        return $layout;
    }

    public function makeTheme(string $slug): ThemeLayout
    {
        /** @var ThemeLayout $layout */
        $layout = $this->makeLayout(ThemeLayout::class, 'theme', $slug);

        return $layout;
    }

    public function getPlugin($slug): ?PluginLayout
    {
        /** @var PluginLayout $layout */
        $layout = $this->getLayout(PluginLayout::class, 'plugin', $slug);

        return $layout;
    }

    public function getTheme($slug): ?ThemeLayout
    {
        /** @var ThemeLayout $layout */
        $layout = $this->getLayout(ThemeLayout::class, 'theme', $slug);

        return $layout;
    }

    protected function makeLayout(string $class, string $type, string $slug): Layout
    {
        $identifier = self::getLayoutIdentifier($type, $slug);

        if (isset($this->layouts[$identifier])) {
            $ucType = ucfirst($type);
            throw new InvalidArgumentException("{$ucType} [{$slug}] is already defined.");
        } elseif ( ! is_subclass_of($class, Layout::class, true)) {
            throw new InvalidArgumentException("{$class} is not a layout class.");
        }

        $container = new Container();

        /** @var Layout $instance */
        $instance = new $class($container);
        $instance->setSlug($slug);

        $container->instance(Layout::class, $instance);

        $this->layouts[$identifier] = $instance;

        return $instance;
    }

    protected function getLayout(string $class, string $type, string $slug): Layout
    {
        $identifier = self::getLayoutIdentifier($type, $slug);

        if ( ! isset($this->layouts[$identifier])) {
            $ucType = ucfirst($type);
            throw new InvalidArgumentException("{$ucType} [{$slug}] is not instantiated.");
        } elseif ( ! is_subclass_of($class, Layout::class, true)) {
            throw new InvalidArgumentException("{$class} is not a layout class.");
        }

        return $this->layouts[$identifier];
    }
}