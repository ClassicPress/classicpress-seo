<?php
/**
 * The Array helpers.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Helpers
 */

namespace Classic_SEO\Helpers;

use ArrayAccess;

/**
 * Arr class.
 */
class Arr {

	/**
	 * Determine whether the given value is array accessible.
	 *
	 * @param mixed $value Value to check.
	 *
	 * @return bool
	 */
	public static function accessible( $value ) {
		return is_array( $value ) || $value instanceof ArrayAccess;
	}

	/**
	 * Determine if the given key exists in the provided array.
	 *
	 * @param ArrayAccess|array $array Array to check key in.
	 * @param string|int        $key   Key to check for.
	 *
	 * @return bool
	 */
	public static function exists( $array, $key ) {
		if ( $array instanceof ArrayAccess ) {
			// @codeCoverageIgnoreStart
			return $array->offsetExists( $key );
			// @codeCoverageIgnoreEnd
		}

		return array_key_exists( $key, $array );
	}

	/**
	 * Get an item from an array.
	 * Supports dot notation:
	 * e.g `Arr::get($array, 'section.subsection.item')`
	 *
	 * @param array      $array   Source array.
	 * @param string     $key     Key to get value for.
	 * @param mixed|null $default Default value if key not exists.
	 *
	 * @return mixed|null
	 */
	public static function get( array $array, $key, $default = null ) {
		return isset( $array[ $key ] ) ? $array[ $key ] : $default;
	}

	/**
	 * Check whether an array or [[\Traversable]] contains an element.
	 *
	 * This method does the same as the PHP function [in_array()](https://secure.php.net/manual/en/function.in-array.php)
	 * but additionally works for objects that implement the [[\Traversable]] interface.
	 *
	 * @throws \InvalidArgumentException If `$array` is neither traversable nor an array.
	 *
	 * @param array|\Traversable $array  The set of values to search.
	 * @param mixed              $search The value to look for.
	 * @param bool               $strict Whether to enable strict (`===`) comparison.
	 *
	 * @return bool `true` if `$search` was found in `$array`, `false` otherwise.
	 */
	public static function includes( $array, $search, $strict = true ) {
		if ( $array instanceof \Traversable ) {
			foreach ( $array as $value ) {
				if ( ( $strict && $search === $value ) || $search == $value ) {
					return true;
				}
			}
		} elseif ( is_array( $array ) ) {
			return in_array( $search, $array, $strict );
		} else {
			throw new \InvalidArgumentException( 'Argument $array must be an array or implement Traversable' );
		}

		return false;
	}

	/**
	 * Insert a single array item inside another array at a set position
	 *
	 * @param array $array    Array to modify. Is passed by reference, and no return is needed.
	 * @param array $new      New array to insert.
	 * @param int   $position Position in the main array to insert the new array.
	 */
	public static function insert( &$array, $new, $position ) {
		$before = array_slice( $array, 0, $position - 1 );
		$after  = array_diff_key( $array, $before );
		$array  = array_merge( $before, $new, $after );
	}

	/**
	 * Push an item onto the beginning of an array.
	 *
	 * @param array $array Array to add.
	 * @param mixed $value Value to add.
	 * @param mixed $key   Add with this key.
	 */
	public static function prepend( &$array, $value, $key = null ) {
		if ( is_null( $key ) ) {
			array_unshift( $array, $value );
			return;
		}

		$array = [ $key => $value ] + $array;
	}

	/**
	 * Update array add or delete value
	 *
	 * @param array $array Array to modify. Is passed by reference, and no return is needed.
	 * @param array $value Value to add or delete.
	 */
	public static function add_delete_value( &$array, $value ) {
		if ( ( $key = array_search( $value, $array ) ) !== false ) { // @codingStandardsIgnoreLine
			unset( $array[ $key ] );
			return;
		}

		$array[] = $value;
	}
}
