<?php
/**
 * The CMB2 fields for the plugin.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin
 */

namespace Classic_SEO\Admin;

use Classic_SEO\Runner;
use Classic_SEO\Traits\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * CMB2_Fields class.
 *
 * @codeCoverageIgnore
 */
class CMB2_Fields implements Runner {

	use Hooker;

	/**
	 * Register hooks.
	 */
	public function hooks() {
		// CMB2 Custom Fields.
		if ( ! has_action( 'cmb2_render_switch' ) ) {
			$this->action( 'cmb2_render_switch', 'render_switch', 10, 5 );
		}
		if ( ! has_action( 'cmb2_render_notice' ) ) {
			$this->action( 'cmb2_render_notice', 'render_notice' );
		}
		if ( ! has_action( 'cmb2_render_address' ) ) {
			$this->action( 'cmb2_render_address', 'render_address', 10, 5 );
		}
	}

	/**
	 * Render switch field.
	 *
	 * @param array  $field             The passed in `CMB2_Field` object.
	 * @param mixed  $escaped_value     The value of this field escaped
	 *                                  It defaults to `sanitize_text_field`.
	 *                                  If you need the unescaped value, you can access it
	 *                                  via `$field->value()`.
	 * @param int    $object_id         The ID of the current object.
	 * @param string $object_type       The type of object you are working with.
	 *                                  Most commonly, `post` (this applies to all post-types),
	 *                                  but could also be `comment`, `user` or `options-page`.
	 * @param object $field_type_object This `CMB2_Types` object.
	 */
	public function render_switch( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

		if ( empty( $field->args['options'] ) ) {
			$field->args['options'] = [
				'off' => esc_html( $field->get_string( 'off', __( 'Off', 'cpseo' ) ) ),
				'on'  => esc_html( $field->get_string( 'on', __( 'On', 'cpseo' ) ) ),
			];
		}
		$field->set_options();

		echo $field_type_object->radio_inline();
	}

	/**
	 * Render notices
	 *
	 * @param array $field The passed in `CMB2_Field` object.
	 */
	public function render_notice( $field ) {
		$hash = [
			'error'   => 'notice notice-alt notice-error error inline',
			'info'    => 'notice notice-alt notice-info info inline',
			'warning' => 'notice notice-alt notice-warning warning inline',
		];

		echo '<div class="' . $hash[ $field->args( 'what' ) ] . '"><p>' . $field->args( 'content' ) . '</p></div>';
	}

	/**
	 * Render address field.
	 *
	 * @param array  $field             The passed in `CMB2_Field` object.
	 * @param mixed  $escaped_value     The value of this field escaped
	 *                                  It defaults to `sanitize_text_field`.
	 *                                  If you need the unescaped value, you can access it
	 *                                  via `$field->value()`.
	 * @param int    $object_id         The ID of the current object.
	 * @param string $object_type       The type of object you are working with.
	 *                                  Most commonly, `post` (this applies to all post-types),
	 *                                  but could also be `comment`, `user` or `options-page`.
	 * @param object $field_type_object This `CMB2_Types` object.
	 */
	public function render_address( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

		// Make sure we assign each part of the value we need.
		$value = wp_parse_args(
			$escaped_value,
			[
				'streetAddress'   => '',
				'addressLocality' => '',
				'addressRegion'   => '',
				'postalCode'      => '',
				'addressCountry'  => '',
			]
		);

		$strings = [
			'streetAddress'   => 'Street Address',
			'addressLocality' => 'Locality',
			'addressRegion'   => 'Region',
			'postalCode'      => 'Postal Code',
			'addressCountry'  => 'Country',
		];

		foreach ( array_keys( $value ) as $id ) :
			echo '<div class="cmb-address-field">' . $field_type_object->input([
				'name'        => $field_type_object->_name( '[' . $id . ']' ),
				'id'          => $field_type_object->_id( '_' . $id ),
				'value'       => $value[ $id ],
				'placeholder' => esc_html( $field->get_string( $id . '_text', $strings[ $id ] ) ),
			]) . '</div>';
		endforeach;
	}
}
