<?php

namespace Naran\Axis\Renderers;

use Naran\Axis\Interfaces\Layout;

class FileRenderer implements Renderer
{
    private array $templateCache = [];

    private Layout $layout;

    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    public function render(
        string $templateName,
        array $context = [],
        string $variant = '',
        bool $echo = true,
        string $extenision = 'php'
    ): string {
        return $this->includeWithContext(
            $this->locateTemplateFile('template', $templateName, $variant, $extenision),
            $context,
            $echo
        );
    }

    public function includeWithContext(string $path, array $context = [], bool $echo = true): string
    {
        if ( ! $path) {
            return '';
        }

        if ( ! empty($context)) {
            extract($context, EXTR_SKIP);
        }

        if ( ! $echo) {
            ob_start();
        }

        /** @noinspection PhpIncludeInspection */
        include $path;

        return $echo ? '' : ob_get_clean();
    }

    public function locateTemplateFile(
        string $type,
        string $tmplName,
        string $variant = '',
        string $extension = 'php'
    ): string {
        $type      = trim($type, '/\\');
        $tmplName  = trim($tmplName, '/\\');
        $variant   = trim($variant, '/\\');
        $cacheName = self::createTemplateCacheName($type, $tmplName, $variant);

        if ( ! ($located = $this->getTemplateCache($cacheName))) {
            $dirname  = dirname($tmplName);
            $filename = wp_basename($tmplName);

            if (empty($dirname)) {
                $dirname = '.';
            }

            $paths          = [];
            $withVariant    = "/{$dirname}/{$filename}-{$variant}.{$extension}";
            $withoutVariant = "/{$dirname}/{$filename}.{$extension}";

            foreach ($this->layout->getTemplatePaths() as $path) {
                if ($variant) {
                    $paths[] = $path . $withVariant;
                }
                $paths[] = $path . $withoutVariant;
            }

            foreach ($paths as $path) {
                if (file_exists($path) && is_readable($path)) {
                    $located = $path;
                    break;
                }
            }

            if ($located) {
                $this->setTemplateCache($cacheName, $located);
            }
        }

        return $located;
    }

    protected function createTemplateCacheName(string $type, string $relpath, string $variant): string
    {
        return "{$this->layout->getSlug()}:{$type}:{$relpath}:{$variant}";
    }

    protected function getTemplateCache(string $templateCacheName): string
    {
        return $this->templateCache[$templateCacheName] ?? '';
    }

    protected function setTemplateCache(string $templateCacheName, string $path)
    {
        $this->templateCache[$templateCacheName] = $path;
    }
}