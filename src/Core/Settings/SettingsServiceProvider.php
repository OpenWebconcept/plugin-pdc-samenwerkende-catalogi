<?php

namespace OWC_SC\Core\Settings;

use OWC_SC\Core\Plugin\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{

	public function register()
	{

		$this->plugin->loader->addFilter('owc/pdc-base/config/settings', $this, 'registerSettings', 10, 1);
	}

	/**
	 * register metaboxes for settings page
	 *
	 * @param $rwmbMetaboxes
	 *
	 * @return array
	 */
	public function registerSettings($pdcBaseMetaboxes)
	{

		$configMetaboxes = $this->plugin->config->get('settings');

		return array_merge($pdcBaseMetaboxes, $configMetaboxes);
	}


}