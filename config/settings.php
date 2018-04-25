<?php

return [

	'samenwerkende_catalogi' => [
		'id'             => 'sc_general',
		'title'          => _x('Samenwerkende Catalogi', 'title on settings page', 'pdc-samenwerkende-catalogi'),
		'settings_pages' => '_owc_pdc_base_settings',
		'tab'            => 'base',
		'fields'         => [
			'sc' => [
				'town_council_label'    => [
					'name' => __('Town council Label', 'pdc-samenwerkende-catalogi'),
					'desc' => __('Label from this list http://standaarden.overheid.nl/owms/terms/Gemeente', 'pdc-samenwerkende-catalogi'),
					'id'   => 'setting_town_council_label',
					'type' => 'text'
				],
				'town_council_uri' => [
					'name' => __('Town council URI', 'pdc-samenwerkende-catalogi'),
					'desc' => __('URI from this list http://standaarden.overheid.nl/owms/terms/Gemeente', 'pdc-samenwerkende-catalogi'),
					'id'   => 'setting_town_council_uri',
					'type' => 'text'
				]
			]
		]
	]
];