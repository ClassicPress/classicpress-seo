<?php
/**
 * The Search Class.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet

 */

namespace Classic_SEO\RichSnippet;

defined( 'ABSPATH' ) || exit;

/**
 * Search_Results class.
 */
class Search_Results implements Snippet {

	/**
	 * Sets the Schema structured data for the SearchResultsPage.
	 *
	 * @link https://schema.org/SearchResultsPage
	 *
	 * @param array  $data   Array of JSON-LD data.
	 * @param JsonLD $jsonld JsonLD Instance.
	 *
	 * @return array
	 */
	public function process( $data, $jsonld ) {
		$data['SearchResultsPage'] = [
			'@context' => 'https://schema.org',
			'@type'    => 'SearchResultsPage',
		];

		return $data;
	}
}
