<?php

namespace OWC_SC\Core\Settings;

use OWC_SC\Core\Plugin\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{

	public function register()
	{

		$this->plugin->loader->addFilter('owc/pdc_base/config/settings', $this, 'registerSettings', 10, 1);
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

		$configMetaboxes = (array)apply_filters('owc/pdc_sc/config/settings', $this->plugin->config->get('settings'));

		return array_merge($pdcBaseMetaboxes, $configMetaboxes);
	}


}