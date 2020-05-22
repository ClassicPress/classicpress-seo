<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Frontend
 */


namespace Classic_SEO\Frontend;

use Classic_SEO\Post;
use Classic_SEO\Helper;
use Classic_SEO\Paper\Paper;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\OpenGraph\Twitter;
use Classic_SEO\OpenGraph\Facebook;
use Classic_SEO\Frontend\Shortcodes;

defined( 'ABSPATH' ) || exit;

/**
 * Frontend class.
 */
class Frontend {

	use Hooker;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->includes();
		$this->hooks();

		/**
		 * Fires when frontend is included/loaded.
		 */
		$this->do_action( 'frontend/loaded' );
	}

	/**
	 * Include required files.
	 */
	private function includes() {

		cpseo()->shortcodes = new Shortcodes;

		new Add_Attributes;
		new Comments;
	}

	/**
	 * Hook into actions and filters.
	 */
	private function hooks() {

		$this->action( 'wp_enqueue_scripts', 'enqueue' );
		$this->action( 'wp', 'integrations' );
		$this->filter( 'the_content_feed', 'embed_rssfooter' );
		$this->filter( 'the_excerpt_rss', 'embed_rssfooter_excerpt' );

		// Reorder categories listing: put primary at the beginning.
		$this->filter( 'get_the_terms', 'reorder_the_terms', 10, 3 );

		// Redirect attachment page to parent post.
		if ( Helper::get_settings( 'general.cpseo_attachment_redirect_urls', true ) ) {
			$this->action( 'wp', 'cpseo_attachment_redirect_urls' );
		}

		// Redirect archives.
		if ( Helper::get_settings( 'titles.cpseo_disable_author_archives' ) || Helper::get_settings( 'titles.cpseo_disable_date_archives' ) ) {
			$this->action( 'wp', 'archive_redirect' );
		}
	}

	/**
	 * Initialize integrations.
	 */
	public function integrations() {
		$type = get_query_var( 'sitemap' );
		if ( ! empty( $type ) || is_customize_preview() ) {
			return;
		}

		Paper::get();
		new Facebook;
		new Twitter;

		// Leave this for backwards compatibility as AMP plugin uses head function. We can remove this in the future update.
		cpseo()->head = new Head;
	}

	/**
	 * Enqueue Styles and Scripts required by plugin.
	 */
	public function enqueue() {
		if ( ! is_admin_bar_showing() || ! Helper::has_cap( 'admin_bar' ) ) {
			return;
		}

		wp_enqueue_style( 'cpseo', cpseo()->assets() . 'css/cpseo.css', null, cpseo()->version );
		wp_enqueue_script( 'cpseo', cpseo()->assets() . 'js/cpseo.js', [ 'jquery' ], cpseo()->version, true );

		if ( is_singular() ) {
			Helper::add_json( 'objectID', Post::get_simple_page_id() );
			Helper::add_json( 'objectType', 'post' );
		} elseif ( is_category() || is_tag() || is_tax() ) {
			Helper::add_json( 'objectID', get_queried_object_id() );
			Helper::add_json( 'objectType', 'term' );
		} elseif ( is_author() ) {
			Helper::add_json( 'objectID', get_queried_object_id() );
			Helper::add_json( 'objectType', 'user' );
		}
	}

	/**
	 * Redirects attachment to its parent post if it has one.
	 */
	public function cpseo_attachment_redirect_urls() {
		global $post;

		// Early bail.
		if ( ! is_attachment() ) {
			return;
		}

		$redirect = ! empty( $post->post_parent ) ? get_permalink( $post->post_parent ) : Helper::get_settings( 'general.cpseo_attachment_redirect_default' );

		/**
		 * Redirect atachment to its parent post.
		 *
		 * @param string  $redirect URL as calculated for redirection.
		 * @param WP_Post $post     Current post instance.
		 */
		Helper::redirect( $this->do_filter( 'frontend/attachment/redirect_url', $redirect, $post ), 301 );
		exit;
	}

	/**
	 * When certain archives are disabled, this redirects those to the homepage.
	 */
	public function archive_redirect() {
		global $wp_query;

		if (
			( Helper::get_settings( 'titles.cpseo_disable_date_archives' ) && $wp_query->is_date ) ||
			( true === Helper::get_settings( 'titles.cpseo_disable_author_archives' ) && $wp_query->is_author )
		) {
			Helper::redirect( get_bloginfo( 'url' ), 301 );
			exit;
		}
	}

	/**
	 * Adds the RSS header and footer messages to the RSS feed item content.
	 *
	 * @param string $content Feed item content.
	 *
	 * @return string
	 */
	public function embed_rssfooter( $content ) {
		return $this->embed_rss( $content, 'full' );
	}

	/**
	 * Adds the RSS header and footer messages to the RSS feed item excerpt.
	 *
	 * @param string $content Feed item excerpt.
	 *
	 * @return string
	 */
	public function embed_rssfooter_excerpt( $content ) {
		return $this->embed_rss( $content, 'excerpt' );
	}

	/**
	 * Inserts the RSS header and footer messages in the RSS feed item.
	 *
	 * @param string $content Feed item content.
	 * @param string $context Feed item context, 'excerpt' or 'full'.
	 *
	 * @return string
	 */
	private function embed_rss( $content, $context = 'full' ) {
		if ( false === $this->can_embed_footer( $content, $context ) ) {
			return $content;
		}

		$before = $this->get_rss_content( 'before' );
		$after  = $this->get_rss_content( 'after' );

		if ( '' === $before && '' === $after ) {
			return $content;
		}

		if ( 'excerpt' === $context && '' !== trim( $content ) ) {
			$content = wpautop( $content );
		}

		return $before . $content . $after;
	}

	/**
	 * Check if we can add the RSS footer and/or header to the RSS feed item.
	 *
	 * @param string $content Feed item content.
	 * @param string $context Feed item context, either 'excerpt' or 'full'.
	 *
	 * @return boolean
	 */
	private function can_embed_footer( $content, $context ) {
		/**
		 * Allow the RSS footer to be dynamically shown/hidden.
		 *
		 * @param bool   $show_embed Indicates if the RSS footer should be shown or not.
		 * @param string $context    The context of the RSS content - 'full' or 'excerpt'.
		 */
		if ( false === $this->do_filter( 'frontend/rss/include_footer', true, $context ) ) {
			return false;
		}

		return is_feed();
	}

	/**
	 * Get rss content for specified location.
	 *
	 * @param string $which Location id.
	 *
	 * @return string
	 */
	private function get_rss_content( $which ) {
		$content = $this->do_filter( 'frontend/rss/' . $which . '_content', Helper::get_settings( 'general.cpseo_rss_' . $which . '_content' ) );

		return '' !== $content ? wpautop( $this->rss_replace_vars( $content ) ) : $content;
	}
	
	/**
	 * Replace variables with the actual values in RSS header and footer messages.
	 *
	 * @param string $content The RSS content.
	 *
	 * @return string
	 */
	private function rss_replace_vars( $content ) {
		global $post;

		/**
		 * Add nofollow for the links in the RSS header and footer messages. Default: true.
		 *
		 * @param bool $unsigned Whether or not to follow the links in RSS feed, defaults to true.
		 */
		$no_follow = $this->do_filter( 'frontend/rss/nofollow_links', true );
		$no_follow = true === $no_follow ? 'rel="nofollow" ' : '';

		$author_link = '';
		if ( is_object( $post ) ) {
			$author_link = '<a ' . $no_follow . 'href="' . esc_url( get_author_posts_url( $post->post_author ) ) . '">' . esc_html( get_the_author() ) . '</a>';
		}
		$post_link      = '<a ' . $no_follow . 'href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>';
		$blog_link      = '<a ' . $no_follow . 'href="' . esc_url( get_bloginfo( 'url' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';
		$blog_desc_link = '<a ' . $no_follow . 'href="' . esc_url( get_bloginfo( 'url' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . ' - ' . esc_html( get_bloginfo( 'description' ) ) . '</a>';

		// Featured image.
		$image = Helper::get_thumbnail_with_fallback( $post->ID, 'full' );
		$image = isset( $image[0] ) ? '<img src="' . $image[0] . '" style="display: block; margin: 1em auto">' : '';

		$content = stripslashes( trim( $content ) );
		$content = str_replace( '%AUTHORLINK%', $author_link, $content );
		$content = str_replace( '%POSTLINK%', $post_link, $content );
		$content = str_replace( '%BLOGLINK%', $blog_link, $content );
		$content = str_replace( '%BLOGDESCLINK%', $blog_desc_link, $content );
		$content = str_replace( '%FEATUREDIMAGE%', $image, $content );

		return $content;
	}

	/**
	 * Reorder terms for a post to put primary category to the beginning.
	 *
	 * @param array|WP_Error $terms    List of attached terms, or WP_Error on failure.
	 * @param int            $post_id  Post ID.
	 * @param string         $taxonomy Name of the taxonomy.
	 *
	 * @return array
	 */
	public function reorder_the_terms( $terms, $post_id, $taxonomy ) {
		/**
		 * Filter: Allow disabling the primary term feature.
		 *
		 * @param bool $return True to disable.
		 */
		if ( true === $this->do_filter( 'primary_term', false ) ) {
			return $terms;
		}

		$post_id = empty( $post_id ) ? $GLOBALS['post']->ID : $post_id;

		// Get Primary Term.
		$primary = absint( Helper::get_post_meta( "primary_{$taxonomy}", $post_id ) );
		if ( ! $primary ) {
			return $terms;
		}

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return [ $primary ];
		}

		$primary_term = null;
		foreach ( $terms as $index => $term ) {
			if ( $primary === $term->term_id ) {
				$primary_term = $term;
				unset( $terms[ $index ] );
				array_unshift( $terms, $primary_term );
				break;
			}
		}

		return $terms;
	}
}
