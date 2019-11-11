<?php
/**
 * The WooCommerce Module
 *
 * @since      0.1.8
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\WooCommerce

 */

namespace ClassicPress_SEO\WooCommerce;

use ClassicPress_SEO\Helper;
use ClassicPress_SEO\Module\Base;
use ClassicPress_SEO\Helpers\Arr;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 */
class Admin extends Base {

	/**
	 * The Constructor.
	 */
	public function __construct() {

		$directory = dirname( __FILE__ );
		$this->config(
			[
				'id'        => 'woocommerce',
				'directory' => $directory,
				'help'      => [
					'title' => esc_html__( 'WooCommerce', 'cpseo' ),
					'view'  => $directory . '/views/help.php',
				],
			]
		);
		parent::__construct();

		// Permalink Manager.
		$this->filter( 'cpseo/settings/general', 'add_general_settings' );
		$this->filter( 'cpseo/flush_fields', 'flush_fields' );
	}

	/**
	 * Add module settings into general optional panel.
	 *
	 * @param array $tabs Array of option panel tabs.
	 *
	 * @return array
	 */
	public function add_general_settings( $tabs ) {
		Arr::insert(
			$tabs,
			[
				'woocommerce' => [
					'icon'  => 'dashicons dashicons-cart',
					'title' => esc_html__( 'WooCommerce', 'cpseo' ),
					'desc'  => esc_html__( 'Choose how you want ClassicPress SEO to handle your WooCommerce SEO. These options help you create cleaner, SEO friendly URLs, and optimize your WooCommerce product pages.', 'cpseo' ),
					'file'  => $this->directory . '/views/options-general.php',
				],
			],
			7
		);
		return $tabs;
	}

	/**
	 * Fields after updation of which we need to flush rewrite rules.
	 *
	 * @param array $fields Fields to flush rewrite rules on.
	 *
	 * @return array
	 */
	public function flush_fields( $fields ) {
		$fields[] = 'cpseo_wc_remove_product_base';
		$fields[] = 'cpseo_wc_remove_category_base';
		$fields[] = 'cpseo_wc_remove_category_parent_slugs';

		return $fields;
	}
}
