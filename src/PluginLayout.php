<?php

namespace Changwoo\Axis;

class PluginLayout extends AbstractLayout
{
    public function init(): void
    {
        $this->standby();
    }

    protected function addDefaultHooks()
    {
        register_activation_hook($this->getMainFile(), [$this, 'activationSetup']);
        register_deactivation_hook($this->getMainFile(), [$this, 'deactivationCleanup']);
        do_action('axis_default_hooks', $this->getSlug());
    }
}
