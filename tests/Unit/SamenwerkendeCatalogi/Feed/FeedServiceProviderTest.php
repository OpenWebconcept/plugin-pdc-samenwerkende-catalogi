<?php

namespace OWC\PDC\SamenwerkendeCatalogi\Tests\Feed;

use Mockery as m;
use OWC\PDC\Base\Foundation\Config;
use OWC\PDC\Base\Foundation\Loader;
use OWC\PDC\Base\Foundation\Plugin;
use OWC\PDC\SamenwerkendeCatalogi\Feed\FeedServiceProvider;
use OWC\PDC\SamenwerkendeCatalogi\Tests\Unit\TestCase;
use WP_Mock;

class FeedServiceProviderTest extends TestCase
{
    public function setUp():void
    {
        WP_Mock::setUp();
    }

    public function tearDown():void
    {
        WP_Mock::tearDown();
    }

    /** @test */
    public function check_registration_feed()
    {
        $config = m::mock(Config::class);
        $plugin = m::mock(Plugin::class);

        $plugin->config = $config;
        $plugin->loader = m::mock(Loader::class);

        $service = new FeedServiceProvider($plugin);

        $plugin->loader->shouldReceive('addAction')->withArgs([
            'owc/config-expander/plugin',
            $service,
            'filterConfigExpanderPlugin',
            10,
            1
        ])->once();

        $plugin->loader->shouldReceive('addAction')->withArgs([
            'init',
            $service,
            'registerFeeds'
        ])->once();

        $plugin->loader->shouldReceive('addFilter')->withArgs([
            'feed_content_type',
            $service,
            'xmlFeedType',
            10,
            2
        ])->once();

        $service->register();

        $this->assertTrue(true);
    }

    /** @test */
    public function check_xml_feed_type()
    {
        $config = m::mock(Config::class);
        $plugin = m::mock(Plugin::class);

        $plugin->config = $config;
        $plugin->loader = m::mock(Loader::class);

        $service = new FeedServiceProvider($plugin);

        WP_Mock::userFunction('feed_content_type', [
                'args'   => 'rss-http',
                'times'  => '1',
                'return' => 'text/xml'
            ]);

        $this->assertEquals('existing_content_type', $service->xmlFeedType('existing_content_type', 'not_sc'));
        $this->assertEquals('text/xml', $service->xmlFeedType('application/octet-stream', 'sc'));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function check_xml_feed_construction()
    {
        $config = m::mock(Config::class);
        $plugin = m::mock(Plugin::class);

        $plugin->config = $config;
        $plugin->loader = m::mock(Loader::class);

        $service = new FeedServiceProvider($plugin);

        $expectedPdcItems = [
            0 =>
                [
                    'ID'                => 5,
                    'post_content'      => 'test content',
                    'post_title'        => 'Test PDC item',
                    'post_excerpt'      => '',
                    'post_name'         => 'test-pdc-item',
                    'post_modified_gmt' => '2018-04-16 10:10:50'
                ]
        ];

        $expectedXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<overheidproduct:scproducten xmlns:overheidproduct=\"http://standaarden.overheid.nl/product/terms/\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:overheid=\"http://standaarden.overheid.nl/owms/terms/\" xmlns:dcterms=\"http://purl.org/dc/terms/\" xsi:schemaLocation=\"http://standaarden.overheid.nl/product/terms/ http://standaarden.overheid.nl/sc/4.0/xsd/sc.xsd\"><overheidproduct:scproduct owms-version=\"4.0\"><overheidproduct:meta><overheidproduct:owmskern><dcterms:identifier><![CDATA[http://owc-pdc.test/onderwerp/test-pdc-item]]></dcterms:identifier><dcterms:title><![CDATA[Test PDC item]]></dcterms:title><dcterms:language><![CDATA[nl]]></dcterms:language><dcterms:type scheme=\"overheid:Informatietype\"><![CDATA[productbeschrijving]]></dcterms:type><dcterms:modified><![CDATA[2018-04-16]]></dcterms:modified><dcterms:spatial scheme=\"overheid:Gemeente\" resourceIdentifier=\"http://standaarden.overheid.nl/owms/terms/Buren_(gemeente)\"><![CDATA[Buren]]></dcterms:spatial><overheid:authority scheme=\"overheid:Gemeente\" resourceIdentifier=\"http://standaarden.overheid.nl/owms/terms/Buren_(gemeente)\"><![CDATA[Buren]]></overheid:authority></overheidproduct:owmskern><overheidproduct:owmsmantel><dcterms:audience scheme=\"overheid:Doelgroep\">particulier</dcterms:audience><dcterms:audience scheme=\"overheid:Doelgroep\">ondernemer</dcterms:audience><dcterms:abstract><![CDATA[test content]]></dcterms:abstract></overheidproduct:owmsmantel><overheidproduct:scmeta><overheidproduct:productID><![CDATA[5]]></overheidproduct:productID><overheidproduct:onlineAanvragen>digid</overheidproduct:onlineAanvragen></overheidproduct:scmeta></overheidproduct:meta><overheidproduct:body/></overheidproduct:scproduct></overheidproduct:scproducten>\n";

        $wp_query = \Mockery::mock('overload:\WP_Query');
        $wp_query->shouldReceive('query')->andReturn($expectedPdcItems);

        $settings = [
            '_owc_setting_portal_url'           => 'http://owc-pdc.test',
            '_owc_setting_portal_pdc_item_slug' => 'onderwerp',
            '_owc_setting_town_council_label'   => 'Buren',
            '_owc_setting_town_council_uri'     => 'http://standaarden.overheid.nl/owms/terms/Buren_(gemeente)'
        ];

        $defaultSettings = [
            '_owc_setting_portal_url'           => '',
            '_owc_setting_portal_pdc_item_slug' => '',
            '_owc_setting_town_council_label'   => '',
            '_owc_setting_town_council_uri'     => '',
        ];

        WP_Mock::userFunction('wp_parse_args', [
                'args'   => [ $settings, $defaultSettings ],
                'times'  => '1',
                'return' => $settings
            ]);

        WP_Mock::userFunction('get_option', [
                'args'   => '_owc_pdc_base_settings',
                'times'  => '1',
                'return' => $settings
            ]);

        WP_Mock::userFunction('trailingslashit', [
                'times'  => '2',
                'return' => function () {
                    return func_get_arg(0) . '/';
                }
            ]);

        $term1       = new \StdClass();
        $term1->slug = 'bewoners';

        $term2       = new \StdClass();
        $term2->slug = 'ondernemers';

        $expectedTerms = [

            0 => $term1,
            1 => $term2
        ];

        WP_Mock::userFunction('get_the_terms', [
                'args' => [ '5', 'pdc-doelgroep'],
                'times'  => '1',
                'return' => $expectedTerms
            ]);

        WP_Mock::userFunction('is_wp_error', [
                'args' => [ $expectedTerms ],
                'times'  => '1',
                'return' => false
            ]);

        WP_Mock::userFunction('wp_trim_words', [
                'args' => [ $expectedPdcItems[0]['post_content'], 60 ],
                'times'  => '1',
                'return' => $expectedPdcItems[0]['post_content']
            ]);

        WP_Mock::userFunction('has_term', [
                'args' => [ 'digid', 'pdc-aspect', $expectedPdcItems[0]['ID'] ],
                'times'  => '1',
                'return' => true
            ]);

        WP_Mock::userFunction('wp_strip_all_tags', [
                'args' => [ $expectedPdcItems[0]['post_content'], true ],
                'times'  => '1',
                'return' => $expectedPdcItems[0]['post_content']
            ]);

        WP_Mock::userFunction('wp_reset_postdata', [
                'times'  => '1'
            ]);

        $this->assertEquals($expectedXML, $service->createXmlFeed());
    }
}
