<?php


namespace Naran\Axis\Layouts;


class PluginLayout extends BaseLayout
{
    private string $mainFile = '';

    public function getMainFile(): string
    {
        return $this->mainFile;
    }

    public function setMainFile(string $mainFile)
    {
        $this->mainFile = $mainFile;
    }

    public function addDefaultHooks()
    {
        register_activation_hook($this->getMainFile(), [$this, 'activationSetup']);
        register_deactivation_hook($this->getMainFile(), [$this, 'deactivationCleanup']);

        do_action('axis_default_plugin_hooks', $this->getSlug());
    }

    public function getTemplatePaths(): array
    {
        return [
            STYLESHEETPATH . "/{$this->getSlug()}",
            TEMPLATEPATH . "/{$this->getSlug()}",
            dirname($this->getMainFile()),
        ];
    }

    public function urlHelper(string $relpath): string
    {
        $relpath = trim($relpath, '/\\');

        if (defined('WP_DEBUG') && WP_DEBUG) {
            $relpath = preg_replace('/\.min\.js$/', '.js', $relpath);
        }

        return plugins_url($relpath, $this->getMainFile());
    }
}
