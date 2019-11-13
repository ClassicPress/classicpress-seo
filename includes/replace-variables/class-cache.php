<?php
/**
 * Variable replacement base.
 *
 * @since      0.3.0
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Replace_Variables
 */


namespace Classic_SEO\Replace_Variables;

defined( 'ABSPATH' ) || exit;

/**
 * Cache class.
 */
class Cache extends Base {

	/**
	 * Cache holder.
	 *
	 * @var array
	 */
	private $cache = [];

	/**
	 * Get from cache.
	 *
	 * @param string $id ID to get from cache.
	 *
	 * @return mixed
	 */
	protected function get_cache( $id ) {
		return isset( $this->cache[ $id ] ) ? $this->cache[ $id ] : '';
	}

	/**
	 * In cache.
	 *
	 * @param string $id ID to get from cache.
	 *
	 * @return bool
	 */
	protected function in_cache( $id ) {
		return isset( $this->cache[ $id ] );
	}

	/**
	 * Save into cache.
	 *
	 * @param string $id    ID to get from cache.
	 * @param mixed  $value Value to save.
	 */
	protected function set_cache( $id, $value ) {
		$this->cache[ $id ] = $value;
	}
}
