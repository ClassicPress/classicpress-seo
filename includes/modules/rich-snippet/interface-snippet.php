<?php
/**
 * The Snippet Interface
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet

 */

namespace Classic_SEO\RichSnippet;

defined( 'ABSPATH' ) || exit;

/**
 * Snippet interface.
 */
interface Snippet {

	/**
	 * Process snippet data
	 *
	 * @param array  $data   Array of json-ld data.
	 * @param JsonLD $jsonld Instance of JsonLD.
	 *
	 * @return array
	 */
	public function process( $data, $jsonld );
}
