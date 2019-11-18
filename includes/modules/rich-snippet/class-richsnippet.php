<?php
/**
 * The Rich Snippet Module
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet
 */


namespace Classic_SEO\RichSnippet;

use Classic_SEO\Traits\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * RichSnippet class.
 */
class RichSnippet {

	use Hooker;

	/**
	 * The Constructor.
	 */
	public function __construct() {

		if ( is_admin() ) {
			new Admin;
		}
		$this->action( 'wp', 'integrations' );

		new Snippet_Shortcode;
	}

	/**
	 * Initialize integrations.
	 */
	public function integrations() {
		$type = get_query_var( 'sitemap' );
		if ( ! empty( $type ) ) {
			return;
		}

		new JsonLD;
	}
}
