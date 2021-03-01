<?php


namespace Naran\Axis\Renderers;


class EjsRenderer implements Renderer
{
    private FileRenderer $fileRenderer;

    private array $queue = [];

    public function __construct(FileRenderer $fileRenderer)
    {
        $this->fileRenderer = $fileRenderer;

        if (is_admin()) {
            if ( ! has_action('admin_print_footer_scripts', [$this, 'render'])) {
                add_action('admin_print_footer_scripts', [$this, 'render']);
            }
        } else {
            if ( ! has_action('wp_print_footer_scripts', [$this, 'render'])) {
                add_action('wp_print_footer_scripts', [$this, 'render']);
            }
        }
    }

    public function enqueue(string $relpath, array $data = [])
    {
        $this->queue[$relpath] = wp_parse_args(
            $data,
            [
                'variant' => '',
                'context' => [],
            ]
        );
    }

    public function render()
    {
        foreach ($this->queue as $relpath => $data) {
            $tmpl_id  = 'tmpl-' . pathinfo(wp_basename($relpath), PATHINFO_FILENAME);
            $template = $this->fileRenderer->locateTemplateFile('ejs', $relpath, $data['variant'], 'ejs');
            if ($template) {
                $content = $this->fileRenderer->render($template, $data['context'], false);
                echo "\n<script type='text/html' id='" . esc_attr($tmpl_id) . "'>\n";
                echo trim(preg_replace('/\s+/', ' ', $content));
                echo "\n</script>\n";
            }
        }
    }
}