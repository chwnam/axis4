<?php
/**
 * Plugin Name:       Axis 4
 * Description:       A WordPress must-use (MU) plugin for developing highly customized, modern PHP based websites.
 * Version:           0.0.0
 * Plugin URI:        https://github.com/chwnam/axis4
 * Author:            Changwoo
 * Author URI:        https://blog.changwoo.pe.kr
 * Textdomain:        axis
 * Domain Path:       languages/
 * Network:           false
 * Requires at least: 5.5
 * Requires PHP:      7.4
 * License:           GPLv2 or later
 * License URI:       https://raw.githubusercontent.com/chwnam/axis4/main/LICENSE
 */

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    define('AXIS4_MAIN', __FILE__);
    define('AXIS4_VERSION', '0.0.0');
} else {
    add_action(
        'admin_notices',
        function () {
            echo '<div class="notice notice-error"><p>'
                 . esc_html__('Please run \'composer dump-autoload\' to run Axis 4 correctly.', 'axis')
                 . '</p></div>';
        }
    );
}
