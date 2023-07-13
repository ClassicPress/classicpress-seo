<?php
/**
 * The Sitemap Stylesheet
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Sitemap

 */

namespace Classic_SEO\Sitemap;

use Classic_SEO\Helper;
use Classic_SEO\Traits\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Stylesheet class.
 */
#[\AllowDynamicProperties]
class Stylesheet extends XML {

	use Hooker;

	/**
	 * Spits out the XSL for the XML sitemap.
	 *
	 * @param string $type Sitemap type.
	 */
	public function output( $type ) {
		$this->type = $type;
		$this->send_headers();

		/* translators: 1. separator, 2. blogname */
		$title = sprintf( __( 'XML Sitemap %1$s %2$s', 'cpseo' ), '-', get_bloginfo( 'name', 'display' ) );

		if ( 'main' !== $type ) {
			/**
			 * Fires for the output of XSL for XML sitemaps, other than type "main".
			 */
			$this->do_action( "sitemap/xsl_{$type}", $title );
			return;
		}

		require_once 'sitemap-xsl.php';
		die;
	}
}
