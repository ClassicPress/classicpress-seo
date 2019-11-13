<?php
/**
 * The Review Class.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet

 */

namespace Classic_SEO\RichSnippet;

use Classic_SEO\Helper;
use Classic_SEO\Traits\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Review class.
 */
class Review implements Snippet {

	use Hooker;

	/**
	 * Review rich snippet.
	 *
	 * @param array  $data   Array of JSON-LD data.
	 * @param JsonLD $jsonld JsonLD Instance.
	 *
	 * @return array
	 */
	public function process( $data, $jsonld ) {
		$entity = [
			'@context'      => 'https://schema.org',
			'@type'         => 'Review',
			'author'        => [
				'@type' => 'Person',
				'name'  => $jsonld->parts['author'],
			],
			'name'          => $jsonld->parts['title'],
			'datePublished' => $jsonld->parts['published'],
			'description'   => $jsonld->parts['desc'],
			'itemReviewed'  => [
				'@type' => 'Thing',
				'name'  => $jsonld->parts['title'],
			],
			'reviewRating'  => [
				'@type'       => 'Rating',
				'worstRating' => Helper::get_post_meta( 'snippet_review_worst_rating' ),
				'bestRating'  => Helper::get_post_meta( 'snippet_review_best_rating' ),
				'ratingValue' => Helper::get_post_meta( 'snippet_review_rating_value' ),
			],
		];

		$jsonld->add_prop( 'thumbnail', $entity['itemReviewed'] );

		$this->filter( 'the_content', 'add_review_to_content', 11 );

		return $entity;
	}

	/**
	 * Injects reviews to content.
	 *
	 * @param  string $content Post content.
	 * @return string
	 *
	 * @since 1.0.12
	 */
	public function add_review_to_content( $content ) {
		$location = $this->get_content_location();
		if ( false === $location ) {
			return $content;
		}

		$review = do_shortcode( '[cpseo_review_snippet]' );

		if ( $this->can_add( 'top', $location ) ) {
			$content = $review . $content;
		}

		if ( $this->can_add( 'bottom', $location ) && $this->can_add_multi_page() ) {
			$content .= $review;
		}

		return $content;
	}

	/**
	 * Check if we can inject the review in the content.
	 *
	 * @return boolean|string
	 */
	private function get_content_location() {
		if ( ! is_main_query() || ! in_the_loop() ) {
			return false;
		}

		/**
		 * Filter: Allow disabling the review display.
		 *
		 * @param bool $return True to disable.
		 */
		if ( true === $this->do_filter( 'snippet/review/hide_data', false ) ) {
			return false;
		}

		$location = $this->do_filter( 'snippet/review/location', Helper::get_post_meta( 'snippet_review_location' ) );
		$location = $location ? $location : 'bottom';

		if ( 'custom' === $location ) {
			return false;
		}

		return $location;
	}

	/**
	 * Check if we can inject the review in the content.
	 *
	 * @param string $where    Adding where.
	 * @param string $location Location to check against.
	 *
	 * @return bool
	 */
	private function can_add( $where, $location ) {
		if ( $where === $location || 'both' === $location ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if we can add content if multipage.
	 *
	 * @return bool
	 */
	private function can_add_multi_page() {
		global $multipage, $numpages, $page;

		return ( ! $multipage || $page === $numpages );
	}
}
