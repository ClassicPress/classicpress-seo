<?php
/**
 * The Service Class.
 *
 * @since      0.1.8
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\RichSnippet

 */

namespace ClassicPress_SEO\RichSnippet;

use ClassicPress_SEO\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Service class.
 */
class Service implements Snippet {

	/**
	 * Service rich snippet.
	 *
	 * @param array  $data   Array of JSON-LD data.
	 * @param JsonLD $jsonld JsonLD Instance.
	 *
	 * @return array
	 */
	public function process( $data, $jsonld ) {
		$price  = Helper::get_post_meta( 'snippet_service_price' );
		$entity = [
			'@context'        => 'https://schema.org',
			'@type'           => 'Service',
			'name'            => $jsonld->parts['title'],
			'description'     => $jsonld->parts['desc'],
			'serviceType'     => Helper::get_post_meta( 'snippet_service_type' ),
			'offers'          => [
				'@type'         => 'Offer',
				'price'         => $price ? $price : '0',
				'priceCurrency' => Helper::get_post_meta( 'snippet_service_price_currency' ),
			],
			'aggregateRating' => [
				'@type'       => 'AggregateRating',
				'ratingValue' => Helper::get_post_meta( 'snippet_service_rating_value' ),
				'ratingCount' => Helper::get_post_meta( 'snippet_service_rating_count' ),
			],
		];

		return $entity;
	}
}
