<?php

return [

    /**
     * Service Providers.
     */
    'providers' => [
        /**
         * Global providers.
         */
	    OWC_SC\Core\Feed\FeedServiceProvider::class,
	    OWC_SC\Core\Settings\SettingsServiceProvider::class,

	    /**
         * Providers specific to the admin.
         */
        'admin'    => [
        ]

    ],

];