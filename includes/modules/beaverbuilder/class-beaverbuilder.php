<?php
/**
 * The BeaverBuilder Module
 *
 * @since      0.5.4
 * @package    Classic_SEO
 * @subpackage Classic_SEO\modules
 */

namespace Classic_SEO\BeaverBuilder;

use Classic_SEO\Helper;
use Classic_SEO\Admin\Admin_Helper;
use Classic_SEO\Traits\Hooker;
use FLBuilderModel;

defined( 'ABSPATH' ) || exit;

/**
 * BeaverBuilder class.
 */
class BeaverBuilder {
	use Hooker;

	/**
	 * The Constructor.
	 */
	public function __construct() {

		add_filter( 'cpseo/sitemap/excluded_post_types', array( $this, 'types' ) );

		if ( ! Admin_Helper::is_post_edit() ) {
			return;
		}
		if ( ! isset( $_GET['post'] ) ) {
			return false;
		}

		$id = $_GET['post'];

		if ( ! get_post_meta( $id, '_fl_builder_enabled', true ) ) {
			return false;
		}

		$this->action( 'cpseo/admin/enqueue_scripts', 'enqueue' );
		$this->action( 'admin_enqueue_scripts', 'dequeue_layout_scripts', 10000 );
	}

	/**
	 * Enqueue styles and scripts for the metabox.
	 */
	public function enqueue() {
		wp_enqueue_script( 'cpseo-bb-post-analysis', cpseo()->plugin_url() . 'assets/admin/js/bb-analysis.js', array( 'cpseo-post-metabox' ), cpseo()->version, true );
		Helper::add_json( 'beaverbuilder', $this->get_config() );
	}

	/**
	 * Get Config data
	 *
	 * @return array The config data.
	 */
	private function get_config() {
		$config = array(
			'pluginName' => 'cpseo-beaverbuilder',
			'pagedata'   => $this->content_data(),
		);

		return $this->do_filter( 'beaverbuilder/config', $config );
	}

	/**
	 * Get Page Layout HTML
	 */
	private function content_data() {

		$id = $_GET['post'];

		ob_start();
		echo do_shortcode( "[fl_builder_insert_layout id=$id]" );
		$data = ob_get_clean();
		FLBuilderModel::delete_all_asset_cache( $id );
		return str_replace( PHP_EOL, '', $data );
	}

	/**
	 * Dequeue any JS enqueued during content_data() function to prevent JS errors.
	 */
	function dequeue_layout_scripts() {
		global $wp_scripts;
		foreach ( $wp_scripts->queue as $item ) {
			if ( false !== strpos( $item, 'fl-builder-layout' ) ) {
				wp_dequeue_script( $item );
			}
		}
	}

	/**
	 * Removes fl-builder-template from sitemap
	 */
	function types( $post_types ) {
		unset( $post_types['fl-builder-template'] );
		return $post_types;
	}

}
