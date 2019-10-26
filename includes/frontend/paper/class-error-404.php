<?php
/**
 * The 404 paper.
 *
 * @since      0.1.8
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\Paper
 */

namespace ClassicPress_SEO\Paper;

defined( 'ABSPATH' ) || exit;

/**
 * 404 Error.
 */
class Error_404 implements IPaper {

	/**
	 * Retrieves the SEO title.
	 *
	 * @return string
	 */
	public function title() {
		return Paper::get_from_options( 'cpseo_404_title', [], esc_html__( 'Page not found', 'cpseo' ) );
	}

	/**
	 * Retrieves the SEO description.
	 *
	 * @return string
	 */
	public function description() {
		return '';
	}

	/**
	 * Retrieves the robots.
	 *
	 * @return string
	 */
	public function robots() {
		return [ 'index' => 'noindex' ];
	}

	/**
	 * Retrieves the canonical URL.
	 *
	 * @return array
	 */
	public function canonical() {
		return [];
	}

	/**
	 * Retrieves meta keywords.
	 *
	 * @return string
	 */
	public function keywords() {
		return '';
	}
}
