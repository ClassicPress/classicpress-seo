<?php
/**
 * The Local Class
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet

 */

namespace Classic_SEO\RichSnippet;

use Classic_SEO\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Local class.
 */
class Local implements Snippet {

	/**
	 * Local rich snippet.
	 *
	 * @param array  $data   Array of JSON-LD data.
	 * @param JsonLD $jsonld JsonLD Instance.
	 *
	 * @return array
	 */
	public function process( $data, $jsonld ) {
		$entity = [
			'@context'                  => 'https://schema.org',
			'@type'                     => 'LocalBusiness',
			'name'                      => $jsonld->parts['title'],
			'url'                       => $jsonld->parts['url'],
			'telephone'                 => Helper::get_post_meta( 'snippet_local_phone' ),
			'priceRange'                => Helper::get_post_meta( 'snippet_local_price_range' ),
			'openingHoursSpecification' => [ '@type' => 'OpeningHoursSpecification' ],
		];

		$jsonld->set_address( 'local', $entity );

		$jsonld->set_data([
			'snippet_local_opendays' => 'dayOfWeek',
			'snippet_local_opens'    => 'opens',
			'snippet_local_closes'   => 'closes',
		], $entity['openingHoursSpecification'] );

		if ( isset( $data['Organization'] ) ) {
			unset( $data['Organization'] );
		}

		return $entity;
	}
}
