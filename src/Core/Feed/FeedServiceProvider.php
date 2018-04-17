<?php

namespace OWC_SC\Core\Feed;

use OWC_SC\Core\Plugin\ServiceProvider;

class FeedServiceProvider extends ServiceProvider
{
	const PREFIX = '_owc_';

	/**
	 * @var $xml DomDocument
	 */
	public $xml;

	/**
	 * @var $settings array
	 */
	private $settings;

	public function register()
	{
		$this->plugin->loader->addAction('init', $this, 'registerFeeds');
		$this->plugin->loader->addFilter('feed_content_type', $this, 'xmlFeedType', 10, 2);
	}

	/**
	 * register custom Feeds.
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
		if ( 'sc' === $type ) {
			return feed_content_type('rss-http');
		}

		return $content_type;
	}

	public function renderXmlFeed()
	{
		echo $this->createXmlFeed();
	}

	public function createXmlFeed()
	{
		$this->settings = get_option(self::PREFIX . 'pdc_base_settings');

		$town_council_label         = esc_attr($this->settings[self::PREFIX.'setting_town_council_label']);
		$town_council_onderwerp_url = esc_url(trailingslashit($this->settings[self::PREFIX.'setting_portal_url']) . trailingslashit($this->settings[self::PREFIX.'setting_portal_pdc_item_slug']));
		$town_council_uri           = esc_url($this->settings[self::PREFIX.'setting_town_council_uri']);

		// "Create" the document.
		$this->xml = new \DOMDocument("1.0", "utf-8");

		$xml_producten = $this->get_sc_root_node();

		$meta_pdc_active_query = [
			[
				'key'     => '_owc_pdc_active',
				'value'   => 1,
				'compare' => '=',
			],
		];

		$args = [
			'post_type'              => 'pdc-item',
			'post_status'            => 'publish',
			'posts_per_page'         => - 1,
			'no_found_rows'          => true, //useful when pagination is not needed.
			'update_post_meta_cache' => false, //useful when post meta will not be utilized.
			'update_post_term_cache' => true, //useful when taxonomy terms will not be utilized.
			'meta_query'             => $meta_pdc_active_query
		];

		$query = new \WP_Query();
		$pdcItems = $query->query($args);

		foreach ( $pdcItems as $pdcItem ) {

			$pdcItem = (array)$pdcItem;

			$doelgroepTerms = get_the_terms($pdcItem['ID'], 'pdc-doelgroep');
			$doelgroepen    = ['particulier'];
			if ( ! is_wp_error($doelgroepTerms) && ! empty($doelgroepTerms) ) {

				$doelgroepen = [];
				foreach ( $doelgroepTerms as $doelgroepTerm ) {

					switch ( $doelgroepTerm->slug ) {
						case 'bewoners':
							$doelgroepen[] = 'particulier';
							break;
						case 'ondernemers':
							$doelgroepen[] = 'ondernemer';
							break;
					}
				}
			}

			$excerpt =  $pdcItem['post_excerpt'];
			if ( empty( $excerpt ) ) {
				$excerpt =  wp_trim_words( $pdcItem['post_content'], 60 );
			}
			$scProductArgs = [
				'id'                         => $pdcItem['ID'],
				'slug'                       => $pdcItem['post_name'],
				'title'                      => $pdcItem['post_title'],
				'excerpt'                    => $excerpt,
				'modified'                   => date('Y-m-d', strtotime($pdcItem['post_modified_gmt'])),
				'digid'                      => has_term('digid', 'pdc-aspect', $pdcItem['ID']),
				'doelgroepen'                => $doelgroepen,
				'town_council_label'         => $town_council_label,
				'town_council_onderwerp_url' => $town_council_onderwerp_url,
				'town_council_uri'           => $town_council_uri
			];

			$scProduct = new ScProductModel($this, $scProductArgs);
			$xml_producten->appendChild($scProduct->getXML());
		}
		wp_reset_postdata();

		$this->xml->appendChild($xml_producten);

		return $this->xml->saveXML();
	}

	private function get_sc_root_node()
	{
		$xml_producten = $this->xml->createElementNS('http://standaarden.overheid.nl/product/terms/', 'overheidproduct:scproducten');

		// add xmlns:overheid="http://standaarden.overheid.nl/owms/terms/"
		$xml_producten->setAttribute(
			'xmlns:overheid',
			'http://standaarden.overheid.nl/owms/terms/'
		);

		// add xmlns:overheidproduct="http://standaarden.overheid.nl/product/terms/"
		$xml_producten->setAttribute(
			'xmlns:overheidproduct',
			'http://standaarden.overheid.nl/product/terms/'
		);

		// add xmlns:dcterms="http://purl.org/dc/terms/"
		$xml_producten->setAttribute(
			'xmlns:dcterms',
			'http://purl.org/dc/terms/'
		);

		$xml_producten->setAttributeNS(
			'http://www.w3.org/2001/XMLSchema-instance',
			'xsi:schemaLocation',
			'http://standaarden.overheid.nl/product/terms/ http://standaarden.overheid.nl/sc/4.0/xsd/sc.xsd'
		);

		return $xml_producten;
	}
}