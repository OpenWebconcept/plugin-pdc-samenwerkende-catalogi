<?php
/**
 * Provider which adds the metabox settings.
 */

namespace OWC\PDC\SamenwerkendeCatalogi\Settings;

use OWC\PDC\Base\Foundation\ServiceProvider;

/**
 * Provider which adds the metabox settings.
 */
class SettingsServiceProvider extends ServiceProvider
{

    /**
     * Registers the hooks.
     */
	public function register()
	{
		$this->plugin->loader->addAction('owc/pdc-base/plugin', $this, 'registerSettings', 10, 1);
	}

    /**
     * Register metaboxes for settings page into pdc-base plugin.
     *
     * @param $basePlugin
     *
     * @return void
     */
	public function registerSettings( $basePlugin )
	{
		$configMetaboxes = $this->plugin->config->get('settings');
		$basePlugin->config->set( 'settings.samenwerkende_catalogi', $configMetaboxes['samenwerkende_catalogi']);
	}
}