<?php
/**
 * An interface for registering hooks with WordPress.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Core
 */

namespace Classic_SEO;

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
