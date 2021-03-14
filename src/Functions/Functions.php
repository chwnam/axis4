<?php

namespace Naran\Axis\Functions;


function doingRequest(string $context): bool
{
    $result = false;

    switch ($context) {
        case 'admin':
            $result = is_admin();
            break;

        case 'ajax':
            $result = wp_doing_ajax();
            break;

        case 'cron':
            $result = wp_doing_cron();
            break;

        case 'front':
            $result = ! (is_admin() || wp_doing_cron() || wp_doing_ajax());
            break;
    }

    return $result;
}
