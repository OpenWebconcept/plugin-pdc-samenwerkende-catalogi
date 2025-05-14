<?php

namespace OWC\PDC\SamenwerkendeCatalogi\Feed;

use DomDocument;
use OWC\PDC\Base\Foundation\ServiceProvider;
use OWC\PDC\SamenwerkendeCatalogi\Foundation\Plugin;
use OWC\PDC\SamenwerkendeCatalogi\Repositories\ScRepository;
use OWC\PDC\SamenwerkendeCatalogi\Settings\SettingsPageOptions;

/**
 * Provider which adds feeds to the WordPress feed.
 */
class FeedServiceProvider extends ServiceProvider
{
    const PREFIX = '_owc_';

    public DomDocument $xml;
    protected SettingsPageOptions $settings;

    /**
     * Construction of the service provider.
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin   = $plugin;
        $this->settings = SettingsPageOptions::make();
    }

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
     */
    public function filterConfigExpanderPlugin($plugin): void
    {
        $plugin->config->set('settings.disable-feed', false);
    }

    /**
     * Registers the custom feeds.
     */
    public function registerFeeds(): void
    {
        add_feed('sc', [$this, 'renderXmlFeed']);
    }

    /**
     * Filter the type, this hook wil set the correct HTTP header for Content-type.
     *
     * @return mixed|void
     */
    public function xmlFeedType(string $contentType, string $type)
    {
        if ('sc' === $type) {
            return feed_content_type('rss-http');
        }

        return $contentType;
    }

    /**
     * Renders the XML feed.
     */
    public function renderXmlFeed(): void
    {
        echo $this->createXmlFeed();
    }

    /**
     * Gathers the data to combine as feed.
     */
    public function createXmlFeed(): string
    {
        $townCouncilLabel = $this->settings->getTownCouncilLabel();
        $townCouncilUri   = $this->settings->getTownCouncilURI();

        // "Create" the document.
        $this->xml = new \DOMDocument("1.0", "utf-8");

        $xmlProducten   = $this->getRootNode();
        $queryArgs      = $this->getQueryArgs();

        foreach ((new ScRepository())->query($queryArgs)->all() as $scItem) {
            $scProductArgs = [
                'id'                         => $scItem->getID(),
                'slug'                       => $scItem->getPostName(),
                'title'                      => $scItem->getTitle(),
                'excerpt'                    => $scItem->getExcerpt(60),
                'modified'                   => $scItem->getPostModified(true)->format('Y-m-d'),
                'digid'                      => has_term('digid', 'pdc-aspect', $scItem->getID()),
                'doelgroepen'                => $scItem->getDoelgroepen(),
                'town_council_label'         => $townCouncilLabel,
                'town_council_onderwerp_url' => $scItem->getTownCouncilOnderwerpUrl(),
                'town_council_uri'           => $townCouncilUri,
                'upl_name'                   => $scItem->getUplName(),
                'upl_resource'               => $scItem->getUplResource(),
            ];

            $scProduct = new ProductEntity($this, $scProductArgs, $this->settings);
            $xmlProducten->appendChild($scProduct->getXML());
        }

        $this->xml->appendChild($xmlProducten);

        return $this->xml->saveXML();
    }

    private function getQueryArgs(): array
    {
        $args = [
            'post_type'              => 'pdc-item',
            'post_status'            => 'publish',
            'posts_per_page'         => -1,
            'no_found_rows'          => true, // Useful when pagination is not needed.
            'update_post_meta_cache' => false, // Useful when post meta will not be utilized.
            'update_post_term_cache' => true, // Useful when taxonomy terms will not be utilized.
            'meta_query'             => $this->getMetaQueryArgs(),
        ];

		if ($taxQueryArgs = $this->getTaxQueryArgs()) {
			$args['tax_query'] = $taxQueryArgs;
		}

		return $args;
    }

	private function getMetaQueryArgs(): array
	{
		return [
            [
                'key'     => '_owc_pdc_active',
                'value'   => '1',
                'compare' => '=',
            ],
        ];
	}

	private function getTaxQueryArgs(): array
	{
		if (! taxonomy_exists('pdc-type')) {
			return [];
		}

		$typeSlugs = get_terms('pdc-type', ['fields' => 'slugs']);

        if (! is_array($typeSlugs) || 1 > count($typeSlugs)) {
            return [];
        }

		return [
            'relation' => 'OR',
            [
                'taxonomy' => 'pdc-type',
                'field'    => 'slug',
                'terms'    => array_filter($typeSlugs, function ($slug) {
                    return 'internal' !== $slug;
                }),
                'operator' => 'IN',
            ],
            [
                'taxonomy' => 'pdc-type',
                'operator' => 'NOT EXISTS',
            ],
        ];
	}

    /**
     * Returns the root node of the xml.
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
