<?php

return [

    /**
     * Service Providers.
     */
    'providers' => [
        /**
         * Global providers.
         */
	    OWC\PDC\SamenwerkendeCatalogi\Feed\FeedServiceProvider::class,
	    OWC\PDC\SamenwerkendeCatalogi\Settings\SettingsServiceProvider::class,

	    /**
         * Providers specific to the admin.
         */
        'admin'    => [
        ]

    ],
	/**
	 * Dependencies upon which the plugin relies.
	 *
	 * Required: type, label
	 * Optional: message
	 *
	 * Type: plugin
	 * - Required: file
	 * - Optional: version
	 *
	 * Type: class
	 * - Required: name
	 */
	'dependencies' => [
		[
			'label' => 'OpenPDC Base',
			'file' => 'pdc-base/pdc-base.php',
			'version' => '2.0.0',
			'type' => ''
		]
	]
];
