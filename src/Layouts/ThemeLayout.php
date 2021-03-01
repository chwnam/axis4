<?php


namespace Naran\Axis\Layouts;


class ThemeLayout extends BaseLayout
{
    public function getMainFile(): string
    {
        return get_stylesheet_directory() . '/functions.php';
    }

    public function addDefaultHooks()
    {
        add_action('after_switch_theme', [$this, 'activationSetup']);
        add_action('switch_theme', [$this, 'deactivationCleanup']);

        do_action('axis_default_theme_hooks', $this->getSlug());
    }

    public function getTemplatePaths(): array
    {
        return [
            STYLESHEETPATH,
            TEMPLATEPATH,
        ];
    }
}
