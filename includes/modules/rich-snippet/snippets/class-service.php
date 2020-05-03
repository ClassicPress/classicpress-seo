<?php
/**
 * The Service Class.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet

 */

namespace Classic_SEO\RichSnippet;

use Classic_SEO\Helper;

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
		$price  = Helper::get_post_meta( 'cpseo_snippet_service_price' );
		$phone_numbers = Helper::get_settings( 'titles.cpseo_phone_numbers' );
		foreach ($phone_numbers as $phone) {
			if ( isset ($phone['number']) && $phone['number'] != '' ) {
				$telephone = $phone['number'];
				break;
			}
		}
		
		$entity = [
			'@context'        => 'https://schema.org',
			'@type'           => 'Service',
			'name'            => $jsonld->parts['title'],
			'description'     => $jsonld->parts['desc'],
			'serviceType'     => Helper::get_post_meta( 'cpseo_snippet_service_type' ),
			'provider' 		  => [
				'@type'		  => 'LocalBusiness',
				'name'		  => $jsonld->get_website_name(),
				'image'		  => Helper::get_settings( 'titles.cpseo_knowledgegraph_logo' ),
				'address'	  => Helper::get_settings( 'titles.cpseo_local_address' ),
				'telephone'   => $telephone,
				'priceRange'  => Helper::get_settings( 'titles.cpseo_price_range' ),
			 ],
			'areaServed'	  => Helper::get_post_meta( 'cpseo_snippet_service_area' ),
			'url'			  => $jsonld->parts['url'],
		];

		return $entity;
	}
}
