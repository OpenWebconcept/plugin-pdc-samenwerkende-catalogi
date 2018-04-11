<?php

namespace OWC_SC\Core\Plugin;

abstract class ServiceProvider
{

    /**
     * Instance of the plugin.
     *
     * @var \OWC_SC\Core\Plugin\BasePlugin
     */
    protected $plugin;

    public function __construct(BasePlugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Register the service provider.
     */
    public abstract function register();

}