<?php
/**
 * An interface for registering hooks with WordPress.
 *
 * @since      0.1.8
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\Core
 */

namespace ClassicPress_SEO;

defined( 'ABSPATH' ) || exit;

/**
 * Runner.
 */
interface Runner {

	/**
	 * Register all hooks to WordPress
	 *
	 * @return void
	 */
	public function hooks();
}
