<?php


namespace Changwoo\Axis\Modules;


trait HookImpl
{
    protected function addAction($tag, $callback, $priority = null, $nargs = 1): self
    {
        $priority = $this->filterPriority($priority);

        if (is_array($tag)) {
            foreach ($tag as $t) {
                add_action($t, $callback, $priority, $nargs);
            }
        } elseif (is_string($tag)) {
            add_action($tag, $callback, $priority, $nargs);
        }

        return $this;
    }

    protected function removeAction($tag, $callback, $priority = null): self
    {
        $priority = $this->filterPriority($priority);
        
        remove_action($tag, $callback, $priority);

        return $this;
    }

    protected function addFilter($tag, $callback, $priority = null, $nargs = 1): self
    {
        $priority = $this->filterPriority($priority);
        
        if (is_array($tag)) {
            foreach ($tag as $t) {
                add_filter($t, $callback, $priority, $nargs);
            }
        } elseif (is_string($tag)) {
            add_filter($tag, $callback, $priority, $nargs);
        }

        return $this;
    }

    protected function removeFilter($tag, $callback, $priority = null): self
    {
        $priority = $this->filterPriority($priority);
        
        remove_filter($tag, $callback, $priority);

        return $this;
    }

    protected function actionOnce($tag, $callback, $priority = null, $nargs = 1): self
    {
        $priority = $this->filterPriority($priority);
        
        if ( ! has_action($callback)) {
            $wrap = function () use (&$wrap, $tag, $callback, $priority) {
                remove_action($tag, $wrap, $priority);
                call_user_func_array($callback, func_get_args());
            };
            add_action($tag, $wrap, $priority, $nargs);
        }

        return $this;
    }

    protected function filterOnce($tag, $callback, $priority = null, $nargs = 1): self
    {
        $priority = $this->filterPriority($priority);

        if ( ! has_filter($callback)) {
            $wrap = function () use (&$wrap, $tag, $callback, $priority) {
                remove_filter($tag, $wrap, $priority);
                call_user_func_array($callback, func_get_args());
            };
            add_filter($tag, $wrap, $priority, $nargs);
        }

        return $this;
    }

    private function filterPriority(?int $priority): int
    {
        return is_null($priority) ? ($this instanceof Module ? $this->getLayout()->getPriority() : 10) : $priority;
    }
}
