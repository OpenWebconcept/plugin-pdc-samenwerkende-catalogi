<?php

namespace OWC\PDC\SamenwerkendeCatalogi\Tests\Settings;

use Mockery as m;
use OWC\PDC\Base\Foundation\Config;
use OWC\PDC\Base\Foundation\Loader;
use OWC\PDC\Base\Foundation\Plugin;
use OWC\PDC\SamenwerkendeCatalogi\Settings\SettingsServiceProvider;
use OWC\PDC\SamenwerkendeCatalogi\Tests\Unit\TestCase;

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
		$plugin = m::mock(Plugin::class);

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
