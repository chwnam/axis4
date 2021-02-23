<?php


namespace Changwoo\Axis;


class ThemeLayout extends AbstractLayout
{
    protected function addDefaultHooks()
    {
        add_action('after_switch_theme', [$this, 'activationSetup']);
        add_action('switch_theme', [$this, 'deactivationCleanup']);
        do_action('axis_default_hooks', $this->getSlug());
    }
}
