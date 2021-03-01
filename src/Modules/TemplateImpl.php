<?php


namespace Naran\Axis\Modules;

use Naran\Axis\Renderers\EjsRenderer;
use Naran\Axis\Renderers\FileRenderer;

trait TemplateImpl
{
    protected function render(
        string $templateName,
        array $context = [],
        string $variant = '',
        bool $echo = true
    ): string {
        /** @var FileRenderer $renderer */
        $renderer = $this->getContainer()->get(FileRenderer::class);

        return $renderer->render($templateName, $context, $variant, $echo);
    }

    protected function enqueueEjs(string $ejsName, array $context = [], string $variant = '')
    {
        /** @var EjsRenderer $ejs */
        $ejs = $this->getContainer()->get(EjsRenderer::class);

        $ejs->enqueue($ejsName, compact('context', 'variant'));
    }
}
