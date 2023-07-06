<?php
/**
 * The Singular Class.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet

 */

namespace Classic_SEO\RichSnippet;

use Classic_SEO\Helper;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Helpers\Conditional;

defined( 'ABSPATH' ) || exit;

/**
 * Singular class.
 */
class Singular implements Snippet {

	use Hooker, Conditional;

	/**
	 * Generate rich snippet.
	 *
	 * @param array  $data   Array of JSON-LD data.
	 * @param JsonLD $jsonld JsonLD Instance.
	 *
	 * @return array
	 */
	public function process( $data, $jsonld ) {
		$schema = $this->can_add_schema( $jsonld );
		if ( false === $schema ) {
			return $data;
		}

		$hook = 'snippet/rich_snippet_' . $schema;
		/**
		 * Short-circuit if 3rd party is interested generating his own data.
		 */
		$pre = $this->do_filter( $hook, false, $jsonld->parts, $data );
		if ( false !== $pre ) {
			$data['richSnippet'] = $this->do_filter( $hook . '_entity', $pre );
			return $data;
		}

		$object = $this->get_schema_class( $schema );
		if ( false === $object ) {
			return $data;
		}

		$entity = $object->process( $data, $jsonld );

		// Images.
		$jsonld->add_prop( 'thumbnail', $entity );
		if ( ! empty( $entity['image'] ) && 'video' === $schema ) {
			$entity['thumbnailUrl'] = $entity['image']['url'];
			unset( $entity['image'] );
		}

		$data['richSnippet'] = $this->do_filter( $hook . '_entity', $entity );

		return $data;
	}

	/**
	 * Get Rich Snippet type.
	 *
	 * @param JsonLD $jsonld JsonLD Instance.
	 *
	 * @return boolean|string
	 */
	private function can_add_schema( $jsonld ) {
		$pages = array_map( 'absint', array_filter( [ Helper::get_settings( 'titles.cpseo_local_seo_about_page' ), Helper::get_settings( 'titles.cpseo_local_seo_contact_page' ) ] ) );
		if ( ! empty( $jsonld->post_id ) && in_array( $jsonld->post_id, $pages, true ) ) {
			return false;
		}

		$schema = Helper::get_post_meta( 'rich_snippet' );
		if (
			! $schema &&
			! metadata_exists( 'post', $jsonld->post_id, 'cpseo_rich_snippet' ) &&
			$schema = Helper::get_settings( "titles.cpseo_pt_{$jsonld->post->post_type}_default_rich_snippet" ) // phpcs:ignore
		) {
			$schema = ( Singular::is_woocommerce_active() || Singular::is_classic_commerce_active() ) && is_product() ? $schema : ( 'article' === $schema ? $schema : '' );
		}

		return $schema;
	}

	/**
	 * Get appropriate Schema Class.
	 *
	 * @param string $schema Schema type.
	 * @return bool|Class
	 */
	private function get_schema_class( $schema ) {
		$data = [
			'article'    => '\\Classic_SEO\\RichSnippet\\Article',
			'book'       => '\\Classic_SEO\\RichSnippet\\Book',
			'course'     => '\\Classic_SEO\\RichSnippet\\Course',
			'event'      => '\\Classic_SEO\\RichSnippet\\Event',
			'jobposting' => '\\Classic_SEO\\RichSnippet\\JobPosting',
			'music'      => '\\Classic_SEO\\RichSnippet\\Music',
			'recipe'     => '\\Classic_SEO\\RichSnippet\\Recipe',
			'restaurant' => '\\Classic_SEO\\RichSnippet\\Restaurant',
			'video'      => '\\Classic_SEO\\RichSnippet\\Video',
			'person'     => '\\Classic_SEO\\RichSnippet\\Person',
			'review'     => '\\Classic_SEO\\RichSnippet\\Review',
			'service'    => '\\Classic_SEO\\RichSnippet\\Service',
			'software'   => '\\Classic_SEO\\RichSnippet\\Software',
			'product'    => '\\Classic_SEO\\RichSnippet\\Product',
		];

		if ( isset( $data[ $schema ] ) && class_exists( $data[ $schema ] ) ) {
			return new $data[ $schema ];
		}

		return false;
	}
}
