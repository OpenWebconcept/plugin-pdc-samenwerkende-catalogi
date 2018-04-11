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

	    /**
         * Providers specific to the admin.
         */
        'admin'    => [
        ]

    ],

];