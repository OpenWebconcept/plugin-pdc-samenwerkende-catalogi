<?php

namespace OWC_SC\Core\Feed;

use OWC_SC\Core\Feed\FeedServiceProvider;

class ScProductModel
{

	/**
	 * Instance of the FeedServiceProvider object
	 *
	 * @var $feed FeedServiceProvider
	 */
	protected $feed;

	protected $meta;

	protected $args;


	public function __construct( FeedServiceProvider $feed, array $args = [] )
	{
		$this->feed = $feed;
		$this->args = $args;
	}

	public function getXML() {

		$scProduct = $this->feed->xml->createElement("overheidproduct:scproduct");
		$scProduct->setAttribute(
			'owms-version',
			'4.0'
		);

		$scProduct->appendChild( $this->getMeta() );

		$scProduct->appendChild( $this->feed->xml->createElement("overheidproduct:body") );

		return $scProduct;
	}

	/**
	 *
	 * @return mixed
	 */
	private function getMeta()
	{
		$meta = $this->feed->xml->createElement("overheidproduct:meta");
		$meta->appendChild($this->getMetaKern());
		$meta->appendChild($this->getMetaMantel());
		$meta->appendChild($this->getMetaSc());

		return $meta;
	}

	/**
	 *
	 * Creates OWMS KERN node
	 *
	 * @return mixed
	 */
	private function getMetaKern()
	{

		$owmskern = $this->feed->xml->createElement("overheidproduct:owmskern");

		$dcterms_items = [
			'identifier',
			'title',
			'language',
			'type',
			'modified',
			'spatial'
		];

		foreach ( $dcterms_items as $dcterm_item ) {

			$dcterm = $this->feed->xml->createElement("dcterms:$dcterm_item");

			switch ( $dcterm_item ) {
				case 'language':
					$cdata_string = 'nl';
					break;
				case 'identifier':
					$cdata_string = $this->args['town_council_onderwerp_url'] . $this->args['slug'];
					break;
				case 'title':
					$cdata_string = $this->args['title'];
					break;
				case 'modified':
					$cdata_string = $this->args['modified'];
					break;
				case 'type':
					$dcterm->setAttribute(
						'scheme',
						'overheid:Informatietype'
					);
					$cdata_string = 'productbeschrijving';
					break;
				case 'spatial':
					$cdata_string = $this->args['town_council_label'];

					$dcterm->setAttribute(
						'scheme',
						'overheid:Gemeente'
					);
					$dcterm->setAttribute(
						'resourceIdentifier',
						$this->args['town_council_uri']
					);
					break;
			}

			$cdata = $this->feed->xml->createCDATASection($cdata_string);
			$dcterm->appendChild($cdata);

			$owmskern->appendChild($dcterm);
		}

		$authority = $this->feed->xml->createElement("overheid:authority");
		$authority->setAttribute(
			'scheme',
			'overheid:Gemeente'
		);
		$authority->setAttribute(
			'resourceIdentifier',
			$this->args['town_council_uri']
		);
		$cdata = $this->feed->xml->createCDATASection($this->args['town_council_label']);
		$authority->appendChild($cdata);

		$owmskern->appendChild($authority);

		return $owmskern;
	}

	/**
	 *
	 * Creates OWMS Mantel node
	 *
	 * @return mixed
	 */
	private function getMetaMantel()
	{
		$owmsmantel = $this->feed->xml->createElement("overheidproduct:owmsmantel");

		foreach ( $this->args['doelgroepen'] as $doelgroep ) {

			$dcterm = $this->feed->xml->createElement("dcterms:audience", $doelgroep);
			$dcterm->setAttribute(
				'scheme',
				'overheid:Doelgroep'
			);
			$owmsmantel->appendChild($dcterm);
		}

		$dcterm = $this->feed->xml->createElement("dcterms:abstract");

		$abstract = wp_strip_all_tags( $this->args['excerpt'], $remove_breaks = true);

		$cdata = $this->feed->xml->createCDATASection($abstract);
		$dcterm->appendChild($cdata);
		$owmsmantel->appendChild($dcterm);

		return $owmsmantel;
	}

	/**
	 *
	 * Creates SC META node
	 *
	 * @return mixed
	 */
	private function getMetaSc()
	{
		$scMeta = $this->feed->xml->createElement("overheidproduct:scmeta");

		$productId = $this->feed->xml->createElement("overheidproduct:productID");
		$cdata     = $this->feed->xml->createCDATASection($this->args['id']);
		$productId->appendChild($cdata);
		$scMeta->appendChild($productId);

		$kenmerk = 'nee';
		if ( true === $this->args['digid'] ) {
			$kenmerk = 'digid';
		}

		$onlineAanvragen = $this->feed->xml->createElement("overheidproduct:onlineAanvragen", $kenmerk);
		$scMeta->appendChild($onlineAanvragen);

		return $scMeta;
	}

}