<?php
/**
 * The Software Class.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet

 */

namespace Classic_SEO\RichSnippet;

use Classic_SEO\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Software class.
 */
class Software implements Snippet {

	/**
	 * Software rich snippet.
	 *
	 * @param array  $data   Array of JSON-LD data.
	 * @param JsonLD $jsonld JsonLD Instance.
	 *
	 * @return array
	 */
	public function process( $data, $jsonld ) {
		$price  = Helper::get_post_meta( 'snippet_software_price' );
		$entity = [
			'@context'            => 'https://schema.org',
			'@type'               => 'SoftwareApplication',
			'name'                => $jsonld->parts['title'],
			'description'         => $jsonld->parts['desc'],
			'operatingSystem'     => Helper::get_post_meta( 'snippet_software_operating_system' ),
			'applicationCategory' => Helper::get_post_meta( 'snippet_software_application_category' ),
			'offers'              => [
				'@type'         => 'Offer',
				'price'         => $price ? $price : '0',
				'priceCurrency' => Helper::get_post_meta( 'snippet_software_price_currency' ),
			],
		];

		$jsonld->add_ratings( 'software', $entity );
		return $entity;
	}
}
