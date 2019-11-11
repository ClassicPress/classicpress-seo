<?php
/**
 * The CollectionPage Class.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet

 */

namespace Classic_SEO\RichSnippet;

use Classic_SEO\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Collection_Page class.
 */
class Collection_Page implements Snippet {

	/**
	 * Sets the Schema structured data for the CollectionPage.
	 *
	 * @link https://schema.org/CollectionPage
	 *
	 * @param array  $data   Array of JSON-LD data.
	 * @param JsonLD $jsonld JsonLD Instance.
	 *
	 * @return array
	 */
	public function process( $data, $jsonld ) {
		$queried_object = get_queried_object();

		/**
		 * Filter to remove snippet data: cpseo/snippet/remove_taxonomy_data.
		 *
		 * @param bool $unsigned Default: false
		 * @param string $unsigned Taxonomy Name
		 */
		if ( true === Helper::get_settings( 'titles.cpseo_remove_' . $queried_object->taxonomy . '_snippet_data' ) || true === apply_filters( 'cpseo/snippet/remove_taxonomy_data', false, $queried_object->taxonomy ) ) {
			return $data;
		}

		$data['CollectionPage'] = [
			'@context'    => 'https://schema.org/',
			'@type'       => 'CollectionPage',
			'headline'    => single_term_title( '', false ),
			'description' => strip_tags( term_description() ),
			'url'         => get_term_link( get_queried_object() ),
			'hasPart'     => $jsonld->get_post_collection( $data ),
		];

		return $data;
	}
}
