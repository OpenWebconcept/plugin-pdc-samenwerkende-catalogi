<?php

return [

	'samenwerkende_catalogi' => [
		'id'             => 'sc_general',
		'title'          => _x('Samenwerkende Catalogi', 'instellingen onderdeel', 'samenwerkende-catalogi'),
		'settings_pages' => '_owc_pdc_base_settings',
		'tab'            => 'base',
		'fields'         => [
			'sc' => [
				'town_council_label'    => [
					'name' => __('Gemeente Label', 'samenwerkende-catalogi'),
					'desc' => __('Label vanuit deze lijst http://standaarden.overheid.nl/owms/terms/Gemeente', 'samenwerkende-catalogi'),
					'id'   => 'setting_town_council_label',
					'type' => 'text'
				],
				'town_council_uri' => [
					'name' => __('Gemeente URI', 'samenwerkende-catalogi'),
					'desc' => __('URI vanuit deze lijst http://standaarden.overheid.nl/owms/terms/Gemeente', 'samenwerkende-catalogi'),
					'id'   => 'setting_town_council_uri',
					'type' => 'text'
				]
			]
		]
	]
];