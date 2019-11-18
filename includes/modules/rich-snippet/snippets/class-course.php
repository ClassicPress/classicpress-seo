<?php
/**
 * The Course Class.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet

 */

namespace Classic_SEO\RichSnippet;

use Classic_SEO\Helper;

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

		$jsonld->add_ratings( 'course', $entity );
		if ( isset( $data['Organization'] ) ) {
			unset( $data['Organization'] );
		}

		return $entity;
	}
}
