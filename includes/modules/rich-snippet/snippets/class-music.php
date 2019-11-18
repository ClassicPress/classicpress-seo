<?php
/**
 * The Music Class
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet

 */

namespace Classic_SEO\RichSnippet;

use Classic_SEO\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Music class.
 */
class Music implements Snippet {

	/**
	 * Music rich snippet.
	 *
	 * @param array  $data   Array of JSON-LD data.
	 * @param JsonLD $jsonld JsonLD Instance.
	 *
	 * @return array
	 */
	public function process( $data, $jsonld ) {
		$entity = [
			'@context'    => 'https://schema.org',
			'@type'       => Helper::get_post_meta( 'snippet_music_type' ),
			'name'        => $jsonld->parts['title'],
			'description' => $jsonld->parts['desc'],
			'url'         => $jsonld->parts['url'],
		];

		return $entity;
	}
}
