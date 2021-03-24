<?php

/**
 * Provider which adds feeds to the WordPress feed.
 */

namespace OWC\PDC\SamenwerkendeCatalogi\Feed;

use OWC\PDC\Base\Foundation\ServiceProvider;
use OWC\PDC\SamenwerkendeCatalogi\Repositories\ScRepository;

/**
 * Provider which adds feeds to the WordPress feed.
 */
class FeedServiceProvider extends ServiceProvider
{
    const PREFIX = '_owc_';

    /**
     * @var DomDocument
     */
    public $xml;

    /**
     * @var array
     */
    private $settings;

    /**
     * Registers the hooks.
     */
    public function register()
    {
        $this->plugin->loader->addAction('owc/config-expander/plugin', $this, 'filterConfigExpanderPlugin', 10, 1);
        $this->plugin->loader->addAction('init', $this, 'registerFeeds');
        $this->plugin->loader->addFilter('feed_content_type', $this, 'xmlFeedType', 10, 2);
    }

    /**
     * Sets the configuration of the config expander.
     *
     * @param $plugin
     *
     * @return void
     */
    public function filterConfigExpanderPlugin($plugin)
    {
        $plugin->config->set('settings.disable-feed', false);
    }

    /**
     * Registers the custom feeds.
     *
     * @return void
     */
    public function registerFeeds()
    {
        add_feed('sc', [$this, 'renderXmlFeed']);
    }

    /**
     * Filter the type, this hook wil set the correct HTTP header for Content-type.
     *
     * @param $content_type
     * @param $type
     *
     * @return mixed|void
     */
    public function xmlFeedType($content_type, $type)
    {
        if ('sc' === $type) {
            return feed_content_type('rss-http');
        }

        return $content_type;
    }

    /**
     * Renders the XML feed.
     *
     * @return string;
     */
    public function renderXmlFeed()
    {
        echo $this->createXmlFeed();
    }

    /**
     * Gathers the data to combine as feed.
     *
     * @return string
     */
    public function createXmlFeed()
    {
        $defaultSettings = [
            '_owc_setting_portal_url'           => '',
            '_owc_setting_portal_pdc_item_slug' => '',
            '_owc_setting_town_council_label'   => '',
            '_owc_setting_town_council_uri'     => '',
        ];

        $this->settings  = wp_parse_args(get_option(self::PREFIX . 'pdc_base_settings'), $defaultSettings);

        $townCouncilLabel   = esc_attr($this->settings[self::PREFIX . 'setting_town_council_label']);
        $townCouncilUri     = esc_url($this->settings[self::PREFIX . 'setting_town_council_uri']);

        // "Create" the document.
        $this->xml = new \DOMDocument("1.0", "utf-8");

        $xmlProducten   = $this->getRootNode();
        $queryArgs      = $this->getQueryArgs();

        foreach ((new ScRepository())->query($queryArgs)->all() as $scItem) {
            $doelgroepen            = $scItem->getDoelgroepen();
            $portalUrl              = $scItem->getPortalURL();

            $scProductArgs = [
                'id'                         => $scItem->getID(),
                'slug'                       => $scItem->getPostName(),
                'title'                      => $scItem->getTitle(),
                'excerpt'                    => $scItem->getExcerpt(60),
                'modified'                   => $scItem->getPostModified(true)->format('Y-m-d'),
                'digid'                      => has_term('digid', 'pdc-aspect', $scItem->getID()),
                'doelgroepen'                => $doelgroepen,
                'town_council_label'         => $townCouncilLabel,
                'town_council_onderwerp_url' => $portalUrl,
                'town_council_uri'           => $townCouncilUri,
                'upl_name'                   => $scItem->getUplName(),
                'upl_resource'               => $scItem->getUplResource(),
            ];

            $scProduct = new ProductEntity($this, $scProductArgs);
            $xmlProducten->appendChild($scProduct->getXML());
        }

        $this->xml->appendChild($xmlProducten);

        return $this->xml->saveXML();
    }

    /**
     * @return array
     */
    private function getQueryArgs(): array
    {
        $meta_pdc_active_query = [
            [
                'key'     => '_owc_pdc_active',
                'value'   => '1',
                'compare' => '=',
            ],
        ];

        return [
            'post_type'              => 'pdc-item',
            'post_status'            => 'publish',
            'posts_per_page'         => -1,
            'no_found_rows'          => true, //useful when pagination is not needed.
            'update_post_meta_cache' => false, //useful when post meta will not be utilized.
            'update_post_term_cache' => true, //useful when taxonomy terms will not be utilized.
            'meta_query'             => $meta_pdc_active_query,
        ];
    }

    /**
     * Returns the root node of the xml.
     *
     * @return object
     */
    private function getRootNode(): object
    {
        $xmlProducten = $this->xml->createElementNS('http://standaarden.overheid.nl/product/terms/', 'overheidproduct:scproducten');

        // add xmlns:overheid="http://standaarden.overheid.nl/owms/terms/"
        $xmlProducten->setAttribute(
            'xmlns:overheid',
            'http://standaarden.overheid.nl/owms/terms/'
        );

        // add xmlns:overheidproduct="http://standaarden.overheid.nl/product/terms/"
        $xmlProducten->setAttribute(
            'xmlns:overheidproduct',
            'http://standaarden.overheid.nl/product/terms/'
        );

        // add xmlns:dcterms="http://purl.org/dc/terms/"
        $xmlProducten->setAttribute(
            'xmlns:dcterms',
            'http://purl.org/dc/terms/'
        );

        $xmlProducten->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation',
            'http://standaarden.overheid.nl/product/terms/ http://standaarden.overheid.nl/sc/4.0/xsd/sc.xsd'
        );

        return $xmlProducten;
    }
}
