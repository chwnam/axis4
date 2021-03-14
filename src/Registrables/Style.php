<?php


namespace Naran\Axis\Registrables;


use Naran\Axis\Interfaces\Registrable;


class Style implements Registrable
{
    public string $handle;

    public string $src;

    public array $deps;

    /**
     * @var null|bool|string
     */
    public $ver;

    public string $media;

    public function __construct(
        string $handle,
        string $src,
        array $deps = [],
        $ver = false,
        string $media = 'all'
    ) {
        $this->handle = $handle;
        $this->src    = $src;
        $this->deps   = $deps;
        $this->ver    = $ver;
        $this->media  = $media;
    }

    public function register()
    {
        if ($this->handle && $this->src && ! wp_style_is($this->handle, 'registered')) {
            wp_register_style($this->handle, $this->src, $this->deps, $this->ver, $this->media);
        }
    }

    public function unregister()
    {
        if ($this->handle && $this->src && wp_style_is($this->handle, 'registered')) {
            wp_deregister_style($this->handle);
        }
    }
}