<?php

namespace OWC\PDC\SamenwerkendeCatalogi\Tests;

/**
 * PHPUnit bootstrap file
 */

/**
 * Load dependencies with Composer autoloader.
 */
require __DIR__ . '/../../vendor/autoload.php';

define('WP_PLUGIN_DIR', __DIR__);

/**
 * Bootstrap WordPress Mock.
 */
\WP_Mock::setUsePatchwork(true);
\WP_Mock::bootstrap();

$GLOBALS['samenwerkende-catalogi'] = [
    'active_plugins' => ['samenwerkende-catalogi/samenwerkende-catalogi.php'],
];

if (! function_exists('get_echo')) {

    /**
     * Capture the echo of a callable function.
     *
     * @param       $callable
     * @param array $args
     *
     * @return string
     */
    function get_echo($callable, $args = [])
    {
        ob_start();
        call_user_func_array($callable, $args);

        return ob_get_clean();
    }
}
