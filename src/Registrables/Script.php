<?php


namespace Naran\Axis\Registrables;


use Naran\Axis\Interfaces\Registrable;


class Script implements Registrable
{
    public string $handle;

    public string $src;

    public array $deps;

    /** @var null|bool|string */
    public $ver;

    public bool $inFooter;

    public function __construct(
        string $handle,
        string $src,
        array $deps = [],
        $ver = false,
        bool $inFooter = false
    ) {
        $this->handle   = $handle;
        $this->src      = $src;
        $this->deps     = $deps;
        $this->ver      = $ver;
        $this->inFooter = $inFooter;
    }

    /**
     * Crate script object for webpacked file, generated by wp-script.
     *
     * @param string $handle Script handle.
     * @param string $path Script's file path.
     * @param string $src Script's URL.
     * @param bool $inFooter Header or footer.
     *
     * @return Script
     */
    public static function wpScript(
        string $handle,
        string $path,
        string $src,
        bool $inFooter = false
    ): Script {
        $asset_file = substr($path, 0, -3) . '.asset.php';
        $deps       = [];
        $ver        = false;

        if (file_exists($asset_file) && is_readable($asset_file)) {
            /**
             * @noinspection PhpIncludeInspection
             * @var          array $asset_file
             */
            $asset = include $asset_file;
            $deps  = $asset['dependencies'] ?? [];
            $ver   = $asset['version'] ?? false;
        } else {
            $handle = '';
            $src    = '';
        }

        return new static($handle, $src, $deps, $ver, $inFooter);
    }

    public function register()
    {
        if ($this->handle && $this->src && ! wp_script_is($this->handle, 'registered')) {
            wp_register_script($this->handle, $this->src, $this->deps, $this->ver, $this->inFooter);
        }
    }

    public function unregister()
    {
        if ($this->handle && $this->src && wp_script_is($this->handle, 'registered')) {
            wp_deregister_script($this->handle);
        }
    }
}