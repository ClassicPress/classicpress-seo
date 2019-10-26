<?php
/**
 * ClassicPress SEO core CLI commands.
 *
 * @since      0.1.8
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\WP_CLI
 */


namespace ClassicPress_SEO\CLI;

use WP_CLI;
use WP_CLI_Command;
use ClassicPress_SEO\Helper;
use ClassicPress_SEO\Sitemap\Cache;
use ClassicPress_SEO\Sitemap\Sitemap_XML;

defined( 'ABSPATH' ) || exit;

/**
 * Commands class.
 */
class Commands extends WP_CLI_Command {

	/**
	 * Generate the sitemap.
	 *
	 * @param array $args Arguments passed.
	 */
	public function sitemap_generate( $args ) {
		$sitemap = Helper::get_module( 'sitemap' );
		if ( false === $sitemap ) {
			WP_CLI::error( 'Sitemap module not active.' );
			return;
		}

		Cache::invalidate_storage();
		$generator = new Sitemap_XML( '1' );
		$generator->get_output();

		WP_CLI::success( 'Sitemap generated.' );
	}
}
