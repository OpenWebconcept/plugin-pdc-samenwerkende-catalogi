<?php

namespace OWC_SC\Core\Settings;

use Mockery as m;
use OWC_SC\Core\Config;
use OWC_SC\Core\Plugin\BasePlugin;
use OWC_SC\Core\Plugin\Loader;
use OWC_SC\Core\Tests\Unit\TestCase;

class SettingsServiceProviderTest extends TestCase
{

	public function setUp()
	{
		\WP_Mock::setUp();
	}

	public function tearDown()
	{
		\WP_Mock::tearDown();
	}

	/** @test */
	public function check_registration_of_settings_metaboxes()
	{
		$config = m::mock(Config::class);
		$plugin = m::mock(BasePlugin::class);

		$plugin->config = $config;
		$plugin->loader = m::mock(Loader::class);

		$service = new SettingsServiceProvider($plugin);

		$plugin->loader->shouldReceive('addFilter')->withArgs([
			'owc/pdc-base/config/settings',
			$service,
			'registerSettings',
			10,
			1
		])->once();

		$service->register();


		$configMetaboxes = [
			'samenwerkende-catalogi' => [
				'id'             => 'metadata',
				'settings_pages' => 'base_settings_page',
				'fields'         => [
					'general' => [
						'testfield_noid' => [
							'type' => 'heading'
						],
						'testfield1'     => [
							'id' => 'metabox_id1'
						],
						'testfield2'     => [
							'id' => 'metabox_id2'
						]
					]
				]
			]
		];

		$existingMetaboxes = [
			'base' => [
				'id'             => 'metadata',
				'settings_pages' => 'base_settings_page',
				'fields'         => [
					'general' => [
						'testfield_noid' => [
							'type' => 'heading'
						],
						'testfield1'     => [
							'id' => 'metabox_id1'
						],
						'testfield2'     => [
							'id' => 'metabox_id2'
						]
					]
				]
			]
		];

		$expectedMetaboxesAfterMerge = [

			'base' => [
				'id'             => 'metadata',
				'settings_pages' => 'base_settings_page',
				'fields'         => [
					'general' => [
						'testfield_noid' => [
							'type' => 'heading'
						],
						'testfield1'     => [
							'id' => 'metabox_id1'
						],
						'testfield2'     => [
							'id' => 'metabox_id2'
						]
					]
				]
			],
			'samenwerkende-catalogi' => [
				'id'             => 'metadata',
				'settings_pages' => 'base_settings_page',
				'fields'         => [
					'general' => [
						'testfield_noid' => [
							'type' => 'heading'
						],
						'testfield1'     => [
							'id' => 'metabox_id1'
						],
						'testfield2'     => [
							'id' => 'metabox_id2'
						]
					]
				]
			]
		];

		$config->shouldReceive('get')->with('settings')->once()->andReturn($configMetaboxes);

		$this->assertEquals($expectedMetaboxesAfterMerge, $service->registerSettings($existingMetaboxes));
	}
}
