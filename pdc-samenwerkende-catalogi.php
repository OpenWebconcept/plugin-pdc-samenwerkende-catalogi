<?php

/**
 * Plugin Name:       PDC Samenwerkende Catalogi
 * Plugin URI:        https://www.openwebconcept.nl/
 * Description:       Plugin to create a XML feed according to the Samenwerkende Catalogi requirements.
 * Version:           2.1.0
 * Author:            Yard | Digital Agency
 * Author URI:        https://www.yard.nl/
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       pdc-samenwerkende-catalogi
 * Domain Path:       /languages
 */

use OWC\PDC\SamenwerkendeCatalogi\Autoloader;
use OWC\PDC\SamenwerkendeCatalogi\Foundation\Plugin;

/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
    die;
}

/**
 * Manual loaded file: the autoloader.
 */
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    require_once __DIR__ . '/autoloader.php';
    $autoloader = new Autoloader();
}

/**
 * Begin execution of the plugin
 *
 * This hook is called once any activated plugins have been loaded. Is generally used for immediate filter setup, or
 * plugin overrides. The plugins_loaded action hook fires early, and precedes the setup_theme, after_setup_theme, init
 * and wp_loaded action hooks.
 */
add_action('plugins_loaded', function () {
    if (! class_exists('OWC\PDC\Base\Foundation\Plugin')) {
        add_action('admin_notices', function () {
            $list = '<p>' . __(
                'The following plugins are required to use the PDC Samenwerkende Catalogi:',
                'pdc-samenwerkende-catalogi'
            ) . '</p><ol><li>OpenPDC Base (version >= 3.0.0)</li></ol>';

            printf('<div class="notice notice-error"><p>%s</p></div>', $list);
        });

        \deactivate_plugins(\plugin_basename(__FILE__));

        return;
    }

    $plugin = (new Plugin(__DIR__))->boot();
}, 10);
