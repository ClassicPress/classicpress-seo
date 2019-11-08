<?php
/**
 * This code adds the OpenGraph output.
 *
 * @since      0.1.8
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\OpenGraph
 */


namespace ClassicPress_SEO\OpenGraph;

use ClassicPress_SEO\Post;
use ClassicPress_SEO\Term;
use ClassicPress_SEO\User;
use ClassicPress_SEO\Helper;
use ClassicPress_SEO\Paper\Paper;
use ClassicPress_SEO\Traits\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * OpenGraph class.
 */
class OpenGraph {

	use Hooker;

	/**
	 * Holds network slug.
	 *
	 * @var string
	 */
	public $network = '';

	/**
	 * Hold meta_key prefix.
	 *
	 * @var string
	 */
	public $prefix = '';

	/**
	 * Schema type.
	 *
	 * @var bool|string
	 */
	protected $schema = false;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->action( 'cpseo/head', 'output_tags', 30 );
	}

	/**
	 * Main OpenGraph output.
	 */
	public function output_tags() {
		wp_reset_query();

		if ( is_singular() && ! is_front_page() ) {
			$this->schema = Post::get_meta( 'rich_snippet' );
			$this->schema = $this->schema ? $this->schema : false;
		}

		/**
		 * Hook to add all OpenGraph metadata
		 *
		 * The dynamic part of the hook name. $this->network, is the network slug.
		 *
		 * @param OpenGraph $this The current opengraph network object.
		 */
		$this->do_action( "opengraph/{$this->network}", $this );
	}

	/**
	 * Get title
	 *
	 * @return bool|string
	 */
	public function get_title() {
		$title = false;
		$key   = $this->prefix . '_title';

		if ( Post::is_simple_page() ) {
			$title = Post::get_meta( $key, Post::get_simple_page_id() );
		} elseif ( is_front_page() ) {
			$title = Helper::get_settings( 'titles.cpseo_homepage_facebook_title' );
		} elseif ( is_category() || is_tax() || is_tag() ) {
			$title = Term::get_meta( $key );
		} elseif ( is_author() ) {
			$title = User::get_meta( $key );
		}

		if ( $title && Helper::get_settings( 'titles.cpseo_capitalize_titles' ) ) {
			$title = ucwords( $title );
		}

		return $title ? $title : Paper::get()->get_title();
	}

	/**
	 * Get description
	 *
	 * @return bool|string
	 */
	public function get_description() {
		$desc = false;
		$key  = $this->prefix . '_description';

		if ( Post::is_simple_page() ) {
			$desc = Post::get_meta( $key, Post::get_simple_page_id() );
			$desc = '' !== $desc ? $desc : $this->fallback_description( 'get_the_excerpt' );
		} elseif ( is_front_page() ) {
			$desc = Helper::get_settings( 'titles.cpseo_homepage_facebook_description' );
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$desc = Term::get_meta( $key );
			$desc = '' !== $desc ? $desc : $this->fallback_description( 'term_description' );
		} elseif ( is_author() ) {
			$desc = User::get_meta( $key );
			$desc = '' !== $desc ? $desc : $this->fallback_description();
		}

		return $desc;
	}

	/**
	 * Get a fallback description.
	 *
	 * @param  string $callback Function name to call.
	 * @return string
	 */
	protected function fallback_description( $callback = false ) {
		$desc = Paper::get()->get_description();
		if ( '' === $desc && $callback ) {
			$desc = $callback();
		}
		return $desc;
	}

	/**
	 * Internal function to output social meta tags.
	 *
	 * @param  string $property Property attribute value.
	 * @param  string $content  Content attribute value.
	 * @return bool
	 */
	public function tag( $property, $content ) {
		$og_property = str_replace( ':', '_', $property );
		/**
		 * Allow developers to change the content of specific social meta tags.
		 *
		 * The dynamic part of the hook name. $this->network, is the network slug
		 * and $og_property, is the property which we are outputting.
		 *
		 * @param string $content The content of the property.
		 */
		$content = $this->do_filter( "opengraph/{$this->network}/$og_property", $content );
		if ( empty( $content ) || ! is_scalar( $content ) ) {
			return false;
		}

		$tag = 'facebook' === $this->network ? 'property' : 'name';
		printf( '<meta %1$s="%2$s" content="%3$s">' . "\n", $tag, esc_attr( $property ), esc_attr( $content ) );

		return true;
	}

	/**
	 * Get Overlay Image URL
	 *
	 * @param string $network The social network.
	 * @return url
	 */
	public function get_overlay_image( $network = 'facebook' ) {
		$img_overlay = '';
		if ( is_singular() ) {
			$img_overlay = Helper::get_post_meta( "{$network}_enable_image_overlay" ) ? Helper::get_post_meta( "{$network}_image_overlay" ) : '';
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$img_overlay = Helper::get_term_meta( "{$network}_enable_image_overlay" ) ? Helper::get_term_meta( "{$network}_image_overlay" ) : '';
		}

		return $img_overlay;
	}
}
