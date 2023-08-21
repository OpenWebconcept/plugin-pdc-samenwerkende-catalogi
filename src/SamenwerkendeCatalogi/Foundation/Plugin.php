<?php

/**
 * The base of the plugin.
 */

namespace OWC\PDC\SamenwerkendeCatalogi\Foundation;

use OWC\PDC\Base\Foundation\Plugin as BasePlugin;
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

/**
 * Sets the name and version of the plugin.
 */
class Plugin extends BasePlugin
{
    /**
     * Name of the plugin.
     *
     * @const string NAME
     */
    public const NAME = 'pdc-samenwerkende-catalogi';

    /**
     * Version of the plugin.
     * Used for setting versions of enqueue scripts and styles.
     *
     * @const string VERSION
     */
    public const VERSION = '2.1.1';

    protected function checkForUpdate()
    {
        if (! class_exists(PucFactory::class) || $this->isExtendedClass()) {
            return;
        }

        try {
            $updater = PucFactory::buildUpdateChecker(
                'https://github.com/OpenWebconcept/plugin-pdc-samenwerkende-catalogi/',
                $this->rootPath . '/pdc-samenwerkende-catalogi.php',
                self::NAME
            );

            $updater->getVcsApi()->enableReleaseAssets();
        } catch (\Throwable $e) {
            error_log($e->getMessage());
        }
    }
}
