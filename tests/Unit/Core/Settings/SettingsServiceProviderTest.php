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

		$plugin->loader->shouldReceive('addAction')->withArgs([
			'owc/pdc-base/plugin',
			$service,
			'registerSettings',
			10,
			1
		])->once();

		$service->register();

		$settings = [
			'samenwerkende_catalogi' => [
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

		$config->shouldReceive('get')->with('settings')->once()->andReturn($settings);

		$basePlugin         = new \StdClass();
		$basePlugin->config = m::mock(Config::class);

		$basePlugin->config->shouldReceive('set')->withArgs( ['settings.samenwerkende_catalogi', $settings['samenwerkende_catalogi']])->once();

		$this->assertTrue( true );

		$service->registerSettings($basePlugin);
	}
}
