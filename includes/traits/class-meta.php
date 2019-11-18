<?php
/**
 * The Metadata.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Traits

 */

namespace Classic_SEO\Traits;

use Classic_SEO\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Meta class.
 */
trait Meta {

	/**
	 * Get meta by object type.
	 *
	 * @param string $object_type Object type for destination where to save.
	 * @param int    $object_id   Object id for destination where to save.
	 * @param string $key         The meta key to retrieve. If no key is provided, fetches all metadata.
	 * @param bool   $single      Whether to return a single value.
	 *
	 * @return mixed
	 */
	protected function get_meta( $object_type, $object_id, $key = '', $single = true ) {
		$func = "get_{$object_type}_meta";

		return $func( $object_id, $key, $single );
	}

	/**
	 * Update meta by object type.
	 *
	 * @param string $object_type Object type for destination where to save.
	 * @param int    $object_id   Object id for destination where to save.
	 * @param string $key         Metadata key.
	 * @param mixed  $value       Metadata value.
	 *
	 * @return mixed
	 */
	protected function update_meta( $object_type, $object_id, $key, $value ) {
		$func = "update_{$object_type}_meta";

		return $func( $object_id, $key, $value );
	}
}
