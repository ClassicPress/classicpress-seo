<?php
/**
 * The Search Results paper.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Paper
 */

namespace Classic_SEO\Paper;

use Classic_SEO\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Search results.
 */
class Search implements IPaper {

	/**
	 * Retrieves the SEO title.
	 *
	 * @return string
	 */
	public function title() {
		return Paper::get_from_options( 'cpseo_search_title', [], 'Searched for %searchphrase% %page% %sep% %sitename%' );
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
		return Helper::get_settings( 'titles.cpseo_noindex_search' ) ? [ 'index' => 'noindex' ] : [];
	}
	
	/**
	 * Retrieves the advanced robots.
	 *
	 * @return array
	 */
	public function advanced_robots() {
		return [];
	}

	/**
	 * Retrieves the canonical URL.
	 *
	 * @return array
	 */
	public function canonical() {
		$search_query = get_search_query();
		return [ 'canonical' => ! empty( $search_query ) && ! preg_match( '|^page/\d+$|', $search_query ) ? get_search_link() : '' ];
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
