<?php
/**
 * The Course Class.
 *
 * @since      0.1.8
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\RichSnippet

 */

namespace ClassicPress_SEO\RichSnippet;

use ClassicPress_SEO\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Course class.
 */
class Course implements Snippet {

	/**
	 * Course rich snippet.
	 *
	 * @param array  $data   Array of JSON-LD data.
	 * @param JsonLD $jsonld JsonLD Instance.
	 *
	 * @return array
	 */
	public function process( $data, $jsonld ) {
		$entity = [
			'@context'    => 'https://schema.org',
			'@type'       => 'Course',
			'name'        => $jsonld->parts['title'],
			'description' => $jsonld->parts['desc'],
			'provider'    => [
				'@type'  => Helper::get_post_meta( 'snippet_course_provider_type' ) ? Helper::get_post_meta( 'snippet_course_provider_type' ) : 'Organization',
				'name'   => Helper::get_post_meta( 'snippet_course_provider' ),
				'sameAs' => Helper::get_post_meta( 'snippet_course_provider_url' ),
			],
		];

		if ( isset( $data['Organization'] ) ) {
			unset( $data['Organization'] );
		}

		return $entity;
	}
}
