<?php

namespace OWC_SC\Core;

use OWC_SC\Core\Plugin\BasePlugin;
use OWC_SC\Core\Admin\Admin;

class Plugin extends BasePlugin
{

    /**
     * Name of the plugin.
     *
     * @var string
     */
    const NAME = 'pdc-samenwerkende-catalogi';

    /**
     * Version of the plugin.
     * Used for setting versions of enqueue scripts and styles.
     *
     * @var string
     */
    const VERSION = '0.1';

    /**
     * Boot the plugin.
     */
	public function boot()
	{
		$this->config->setPluginName($this->getName());
		$this->config->setFilterExceptions(['core']);
		$this->config->boot();

		$this->bootServiceProviders();

		if ( is_admin() ) {
			$admin = new Admin($this);
			$admin->boot();
		}

		$this->loader->addAction( 'init', $this->config, 'filter', 9);
		$this->loader->register();
	}
}