<?php
/**
 * The <head> tag.
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
use Classic_SEO\Sitemap\Router;
use Classic_SEO\Helpers\Str;

defined( 'ABSPATH' ) || exit;

/**
 * Head class.
 */
class Head {

	use Hooker;

	/**
	 * The Constructor.
	 */
	public function __construct() {

		$this->action( 'wp_head', 'head', 1 );

		$this->filter( 'language_attributes', 'search_results_schema' );

		// The head function here calls action cpseo/head, to which we hook all our functionality.
		$this->action( 'cpseo/head', 'metadesc', 6 );
		$this->action( 'cpseo/head', 'robots', 10 );
		$this->action( 'cpseo/head', 'canonical', 20 );
		$this->action( 'cpseo/head', 'adjacent_rel_links', 21 );
		$this->action( 'cpseo/head', 'metakeywords', 22 );

		$this->filter( 'wp_title', 'title', 15 );
		$this->filter( 'thematic_doctitle', 'title', 15 );
		$this->filter( 'pre_get_document_title', 'title', 15 );

		// Code to move title inside the Classic SEO's meta.
		remove_action( 'wp_head', '_wp_render_title_tag', 1 );
		add_action( 'cpseo/head', '_wp_render_title_tag', 1 );

		// Force Rewrite title.
		if ( Helper::get_settings( 'titles.cpseo_rewrite_title' ) && ! current_theme_supports( 'title-tag' ) ) {
			$this->action( 'get_header', 'start_ob', 0 );
			$this->action( 'wp_head', 'rewrite_title', 9999 );
		}
	}

	/**
	 * Add Search Result Page schema as language attributes for the <html> tag.
	 *
	 * @param  string $output A space-separated list of language attributes.
	 * @return string
	 */
	public function search_results_schema( $output ) {
		if ( ! is_search() ) {
			return $output;
		}

		return preg_replace( '/itemtype="([^"]+)"/', 'itemtype="https://schema.org/SearchResultsPage', $output );
	}

	/**
	 * Main function attached to the wp_head hook.
	 */
	public function head() {
		global $wp_query;

		$old_wp_query = null;
		if ( ! $wp_query->is_main_query() ) {
			$old_wp_query = $wp_query;
			wp_reset_query();
		}

		// Remove core actions, now handled by Classic SEO.
		remove_action( 'wp_head', 'rel_canonical' );
		remove_action( 'wp_head', 'index_rel_link' );
		remove_action( 'wp_head', 'start_post_rel_link' );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
		remove_action( 'wp_head', 'noindex', 1 );

		/**
		 * Add extra output in the head tag.
		 */
		$this->do_action( 'head' );

		if ( ! empty( $old_wp_query ) ) {
			$GLOBALS['wp_query'] = $old_wp_query;
			unset( $old_wp_query );
		}
	}

	/**
	 * Main title function.
	 *
	 * @param  string $title Already set title or empty string.
	 * @return string
	 */
	public function title( $title ) {
		if ( is_feed() ) {
			return $title;
		}

		$generated = Paper::get()->get_title();
		return Str::is_non_empty( $generated ) ? $generated : $title;
	}

	/**
	 * Output the meta description tag with the generated description.
	 */
	public function metadesc() {
		$generated = Paper::get()->get_description();

		if ( Str::is_non_empty( $generated ) ) {
			echo '<meta name="description" content="', $generated, '"/>', "\n";
		} elseif ( Helper::has_cap( 'general' ) && is_singular() ) {
			echo '<!-- ', \html_entity_decode( esc_html__( 'Admin only notice: this page has no meta description set. Please edit the page to add one, or setup a template in Classic SEO -> Titles &amp; Metas.', 'cpseo' ) ), ' -->', "\n";
		}
	}

	/**
	 * Output the meta robots tag.
	 */
	public function robots() {
		$robots    = Paper::get()->get_robots();
		$robotsstr = join( ',', $robots );
		if ( Str::is_non_empty( $robotsstr ) ) {
			echo '<meta name="robots" content="', esc_attr( $robotsstr ), '"/>', "\n";
		}

		// If a page is noindex, let's remove the canonical URL.
		// https://www.seroundtable.com/google-noindex-rel-canonical-confusion-26079.html .
		if ( isset( $robots['index'] ) && 'noindex' === $robots['index'] ) {
			$this->remove_action( 'cpseo/head', 'canonical', 20 );
			$this->remove_action( 'cpseo/head', 'adjacent_rel_links', 21 );
		}
	}

	/**
	 * Output the canonical URL tag.
	 */
	public function canonical() {
		$canonical = Paper::get()->get_canonical();
		if ( Str::is_non_empty( $canonical ) ) {
			echo '<link rel="canonical" href="' . esc_url( $canonical, null, 'other' ) . '" />' . "\n";
		}
	}

	/**
	 * Add the rel 'prev' and 'next' links to archives or single posts.
	 *
	 * @link http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
	 */
	public function adjacent_rel_links() {
		/**
		 * Enable rel "next" & "prev" tags on sites running Genesis.
		 *
		 * @param bool $unsigned Whether or not to show rel next / prev .
		 */
		if ( is_home() && function_exists( 'genesis' ) && false === $this->do_filter( 'frontend/genesis/force_adjacent_rel_home', false ) ) {
			return;
		}

		/**
		 * Disable rel 'prev' and 'next' links.
		 *
		 * @param bool $disable Rel 'prev' and 'next' links should be disabled or not.
		 */
		if ( true === $this->do_filter( 'frontend/disable_adjacent_rel_links', false ) ) {
			return;
		}

		if ( is_singular() ) {
			$this->adjacent_rel_links_single();
			return;
		}
		$this->adjacent_rel_links_archive();
	}

	/**
	 * Output the meta keywords value.
	 */
	public function metakeywords() {
		$keywords = Paper::get()->get_keywords();
		if ( Str::is_non_empty( $keywords ) ) {
			echo '<meta name="keywords" content="', esc_attr( $keywords ), '"/>', "\n";
		}
	}

	/**
	 * Output the rel next/prev tags on a paginated single post.
	 *
	 * @return void
	 */
	private function adjacent_rel_links_single() {
		$num_pages = 1;

		$queried_object = get_queried_object();
		if ( ! empty( $queried_object ) ) {
			$num_pages = substr_count( $queried_object->post_content, '<!--nextpage-->' ) + 1;
		}

		if ( 1 === $num_pages ) {
			return;
		}

		$page = max( 1, (int) get_query_var( 'page' ) );
		$url  = get_permalink( get_queried_object_id() );

		if ( $page > 1 ) {
			$this->adjacent_rel_link( 'prev', $url, $page - 1, 'page' );
		}

		if ( $page < $num_pages ) {
			$this->adjacent_rel_link( 'next', $url, $page + 1, 'page' );
		}
	}

	/**
	 * Output the rel next/prev tags on archives.
	 */
	private function adjacent_rel_links_archive() {
		$url = Paper::get()->get_canonical( true, true );
		if ( ! is_string( $url ) || '' === $url ) {
			return;
		}

		$paged = max( 1, (int) get_query_var( 'paged' ) );
		if ( 2 === $paged ) {
			$this->adjacent_rel_link( 'prev', $url, $paged - 1 );
		}

		if ( is_front_page() ) {
			$url = Router::get_base_url( '' );
		}

		if ( $paged > 2 ) {
			$this->adjacent_rel_link( 'prev', $url, $paged - 1 );
		}

		if ( $paged < $GLOBALS['wp_query']->max_num_pages ) {
			$this->adjacent_rel_link( 'next', $url, $paged + 1 );
		}
	}

	/**
	 * Build adjacent page link for archives.
	 *
	 * @param string $rel       Prev or next.
	 * @param string $url       The current archive URL without page parameter.
	 * @param string $page      The page number added to the $url in the link tag.
	 * @param string $query_arg The pagination query argument to use for the $url.
	 */
	private function adjacent_rel_link( $rel, $url, $page, $query_arg = 'paged' ) {
		global $wp_rewrite;

		if ( $page > 1 ) {
			$url = ! $wp_rewrite->using_permalinks() ? add_query_arg( $query_arg, $page, $url ) : user_trailingslashit( trailingslashit( $url ) . $this->get_pagination_base() . $page );
		}

		/**
		 * Change the link rel HTML output.
		 *
		 * @param string $link The `<link rel=""` tag.
		 */
		$link = $this->do_filter( "frontend/{$rel}_rel_link", '<link rel="' . esc_attr( $rel ) . '" href="' . esc_url( $url ) . "\" />\n" );
		if ( Str::is_non_empty( $link ) ) {
			echo $link;
		}
	}

	/**
	 * Get pagination base.
	 *
	 * @return string The pagination base.
	 */
	private function get_pagination_base() {
		global $wp_rewrite;

		return ( ! is_singular() || Post::is_home_static_page() ) ? trailingslashit( $wp_rewrite->pagination_base ) : '';
	}


	/**
	 * Start the Output Buffer.
	 *
	 * @since 1.0.29
	 */
	public function start_ob() {
		ob_start();
	}

	/**
	 * Use output buffering to force rewrite the title tag.
	 */
	public function rewrite_title() {
		global $wp_query;

		// Check if we're in the main query.
		$old_wp_query = null;
		if ( ! $wp_query->is_main_query() ) {
			$old_wp_query = $wp_query;
			wp_reset_query();
		}

		$content = ob_get_clean();
		$title   = Paper::get()->get_title();
		if ( empty( $title ) ) {
			echo $content;
		}

		// Find all title tags, remove them, and add the new one.
		$content = preg_replace( '/<title.*?\/title>/i', '', $content );
		$content = str_replace( '<head>', '<head>' . "\n" . '<title>' . esc_html( $title ) . '</title>', $content );
		if ( ! empty( $old_wp_query ) ) {
			$GLOBALS['wp_query'] = $old_wp_query;
		}

		echo $content;
	}
}
