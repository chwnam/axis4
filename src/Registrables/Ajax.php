<?php


namespace Changwoo\Axis\Registrables;


class Ajax implements Registrable
{
    public string $action;

    /** @var string|array|callable */
    public $callback;

    /** @var bool|string */
    public $allowNopriv;

    public bool $wcAjax;

    public int $priority;

    public function __construct(
        string $action,
        $callback,
        $allowNopriv = false,
        bool $wcAjax = false,
        int $priority = 10
    ) {
        $this->action      = $action;
        $this->callback    = $callback;
        $this->allowNopriv = $allowNopriv;
        $this->wcAjax      = $wcAjax;
        $this->priority    = $priority;
    }

    public function register()
    {
        if ($this->action && is_callable($this->callback)) {
            if ($this->wcAjax) {
                add_action("wc_ajax_{$this->action}", $this->callback, $this->priority);
            } else {
                if ('nopriv_only' === $this->allowNopriv || true === $this->allowNopriv) {
                    add_action("wp_ajax_nopriv_{$this->action}", $this->callback, $this->priority);
                }
                if (is_bool($this->allowNopriv)) {
                    add_action("wp_ajax_{$this->action}", $this->callback, $this->priority);
                }
            }
        }
    }

    public function unregister()
    {
        if ($this->action && is_callable($this->callback)) {
            if ($this->wcAjax) {
                remove_action("wc_ajax_{$this->action}", $this->callback, $this->priority);
            } else {
                if ('nopriv_only' === $this->allowNopriv || true === $this->allowNopriv) {
                    remove_action("wp_ajax_nopriv_{$this->action}", $this->callback, $this->priority);
                }
                if (is_bool($this->allowNopriv)) {
                    remove_action("wp_ajax_{$this->action}", $this->callback, $this->priority);
                }
            }
        }
    }
}