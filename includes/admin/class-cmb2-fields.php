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
use Classic_SEO\Admin\Admin_Helper;

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
		if ( ! has_action( 'cmb2_render_advanced_robots' ) ) {
			$this->action( 'cmb2_render_advanced_robots', 'render_advanced_robots', 10, 5 );
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
	
	/**
	 * Render Advanced Robots fields.
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
	public function render_advanced_robots( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		// Make sure we assign each part of the value we need.
		$values = wp_parse_args(
			$escaped_value,
			[
				'max-snippet'       => -1,
				'max-video-preview' => -1,
				'max-image-preview' => 'large',
			]
		);

		$strings = [
			'max-snippet'       => __( 'Max Snippet', 'cpseo' ) . Admin_Helper::get_tooltip( esc_html__( 'Specify a maximum text-length, in characters, of a snippet for your page.', 'cpseo' ) ),
			'max-video-preview' => __( 'Max Video Preview', 'cpseo' ) . Admin_Helper::get_tooltip( esc_html__( 'Specify a maximum duration in seconds of an animated video preview.', 'cpseo' ) ),
			'max-image-preview' => __( 'Max Image Preview', 'cpseo' ) . Admin_Helper::get_tooltip( esc_html__( 'Specify a maximum size of image preview to be shown for images on this page.', 'cpseo' ) ),
		];

		echo '<ul class="cmb-advanced-robots-list no-select-all cmb2-list cmb-advanced-robots-field">';
		foreach ( $values as $id => $value ) :
			$value = isset( $escaped_value[ $id ] ) ? $escaped_value[ $id ] : $value;

			echo '<li>';
				echo '<label for="' . $field_type_object->_id( '_' . $id . '_name' ) . '">';
					echo $field_type_object->checkbox([
						'name'    => $field_type_object->_name( "[{$id}][enable]" ),
						'id'      => $field_type_object->_id( '_' . $id . '_name' ),
						'value'   => true,
						'checked' => ! empty( $escaped_value[ $id ] ) || empty( $escaped_value ) ? 'checked' : false,
					]);
				echo $field->get_string( $id . '_text', $strings[ $id ] ) . '</label>';

			if ( 'max-image-preview' === $id ) {
				echo $field_type_object->select([
					'name'    => $field_type_object->_name( "[{$id}][length]" ),
					'id'      => $field_type_object->_id( '_' . $id . '_name' ),
					'options' => $this->get_image_sizes( $value ),
				]);
			}

			if ( 'max-image-preview' !== $id ) {
				echo $field_type_object->input([
					'name'  => $field_type_object->_name( "[{$id}][length]" ),
					'id'    => $field_type_object->_id( '_' . $id . '_length' ),
					'value' => $value ? $value : -1,
					'type'  => 'number',
					'min'   => -1,
				]);
			}

			echo '</li>';
		endforeach;
		echo '</ul>';
	}

	/**
	 * Get Image sizes.
	 *
	 * @param  string $size    The selected image size.
	 * @return string $options The image sizes.
	 */
	private function get_image_sizes( $size = 'large' ) {
		$values  = [
			'large'    => __( 'Large', 'cpseo' ),
			'standard' => __( 'Standard', 'cpseo' ),
			'none'     => __( 'None', 'cpseo' ),
		];
		$options = '';
		foreach ( $values as $data => $label ) {
			$options .= '<option value="' . $data . '" ' . selected( $size, $data, false ) . ' >' . $label . '</option>';
		}

		return $options;
	}
}
