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
        'admin' => [],
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
            'type'    => 'plugin',
            'label'   => 'OpenPDC Base',
            'version' => '3.0.0',
            'file'    => 'pdc-base/pdc-base.php',
        ],
    ],
];
