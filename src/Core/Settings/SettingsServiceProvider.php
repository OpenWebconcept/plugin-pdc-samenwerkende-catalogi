<?php

namespace OWC_SC\Core\Settings;

use OWC_SC\Core\Plugin\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{

	public function register()
	{
		$this->plugin->loader->addAction('owc/pdc-base/plugin', $this, 'registerSettings', 10, 1);
	}

	/**
	 * register metaboxes for settings page into pdc-base plugin
	 *
	 * @param $plugin
	 *
	 * @return $plugin OWC_PDC_Base\Core\Plugin
	 */
	public function registerSettings( $basePlugin )
	{
		$configMetaboxes = $this->plugin->config->get('settings');
		$basePlugin->config->set( 'settings.samenwerkende_catalogi', $configMetaboxes['samenwerkende_catalogi']);
	}


}