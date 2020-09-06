<?php
/**
 * The Redirections Metabox
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Redirections

 */

namespace Classic_SEO\Redirections;

use Classic_SEO\Helper;
use Classic_SEO\Traits\Hooker;

/**
 * Metabox class.
 *
 * @codeCoverageIgnore
 */
class Metabox {

	use Hooker;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->action( 'cpseo/metabox/settings/advanced', 'metabox_settings_advanced' );
		$this->action( 'cpseo/metabox/process_fields', 'save_advanced_meta' );
	}

	/**
	 * Add settings in the Advanced tab of the metabox.
	 *
	 * @param CMB2 $cmb The CMB2 metabox object.
	 */
	public function metabox_settings_advanced( $cmb ) {
		$redirection = Cache::get_by_object_id( $cmb->object_id, $cmb->object_type() );

		$url = parse_url( get_permalink( $cmb->object_id ), PHP_URL_PATH );
		$url = trim( $url, '/' );

		$redirection = $redirection ? DB::get_redirection_by_id( $redirection->cpseo_redirection_id, 'active' ) : [
			'id'          => '',
			'url_to'      => '',
			'header_code' => Helper::get_settings( 'general.cpseo_redirections_header_code' ),
		];

		$message = ! empty( $redirection['id'] ) ? esc_html__( 'Edit redirection for the URL of this post.', 'cpseo' ) :
			esc_html__( 'Create new redirection for the URL of this post.', 'cpseo' );

		$cmb->add_field([
			'id'   => 'cpseo_redirection_heading',
			'type' => 'title',
			'name' => esc_html__( 'Redirect', 'cpseo' ),
			'desc' => $message . ' ' . esc_html__( 'Publish or update the post to save the redirection.', 'cpseo' ),
		]);

		$cmb->add_field([
			'id'         => 'cpseo_redirection_header_code',
			'type'       => 'select',
			'name'       => esc_html__( 'Redirection Type', 'cpseo' ),
			'options'    => Helper::choices_redirection_types(),
			'default'    => isset( $redirection['header_code'] ) ? $redirection['header_code'] : '',
			'save_field' => false,
		]);

		$cmb->add_field([
			'id'         => 'cpseo_redirection_url_to',
			'type'       => 'text',
			'name'       => esc_html__( 'Destination URL', 'cpseo' ),
			'save_field' => false,
			'default'    => isset( $redirection['url_to'] ) ? $redirection['url_to'] : '',
		]);

		$cmb->add_field([
			'id'         => 'cpseo_redirection_id',
			'type'       => 'hidden',
			'save_field' => false,
			'default'    => isset( $redirection['id'] ) ? $redirection['id'] : '',
		]);

		$cmb->add_field([
			'id'         => 'cpseo_redirection_sources',
			'type'       => 'hidden',
			'save_field' => false,
			'default'    => $url,
		]);
	}

	/**
	 * Save handler for metadata.
	 *
	 * @param CMB2 $cmb CMB2 instance.
	 */
	public function save_advanced_meta( $cmb ) {
		if ( empty( $cmb->data_to_save['cpseo_redirection_url_to'] ) ) {
			// Delete.
			if ( ! empty( $cmb->data_to_save['cpseo_redirection_id'] ) ) {
				DB::delete( $cmb->data_to_save['cpseo_redirection_id'] );
				Helper::add_notification( esc_html__( 'Redirection successfully deleted.', 'cpseo' ), [ 'type' => 'info' ] );
			}
			return;
		}

		// Check if no change bail!!
		if ( ! $this->can_update( $cmb->data_to_save ) ) {
			return;
		}

		$values = [
			'id'          => $cmb->data_to_save['cpseo_redirection_id'],
			'url_to'      => $cmb->data_to_save['cpseo_redirection_url_to'],
			'sources'     => [
				[
					'pattern'    => $cmb->data_to_save['cpseo_redirection_sources'],
					'comparison' => 'exact',
				],
			],
			'header_code' => $cmb->data_to_save['cpseo_redirection_header_code'],
		];

		$redirection_id = DB::update_iff( $values );
		if ( ! isset( $values['id'] ) ) {
			Helper::add_notification( esc_html__( 'New redirection created.', 'cpseo' ) );
		}

		Cache::add([
			'from_url'       	=> $cmb->data_to_save['cpseo_redirection_sources'],
			'redirection_id'	=> $redirection_id,
			'object_id'			=> $cmb->object_id,
		]);
	}

	/**
	 * Check if update is required.
	 *
	 * @param  array $values Values.
	 * @return boolean
	 */
	private function can_update( $values ) {
		if ( did_action( 'cpseo/redirection/post_updated' ) ) {
			return false;
		}

		if ( empty( $values['cpseo_redirection_id'] ) ) {
			return true;
		}

		$redirection = DB::get_redirection_by_id( $values['cpseo_redirection_id'] );

		return ! ( $values['cpseo_redirection_url_to'] === $redirection['url_to'] );
	}
}
