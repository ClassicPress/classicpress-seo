<?php
/**
 * The WooCommerce register variables.
 *
 * @since      1.0.32
 * @package    Classic_SEO
 * @subpackage Classic_SEO\WooCommerce

 */

namespace Classic_SEO\WooCommerce;

use Classic_SEO\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * WC Variables class.
 */
class WC_Vars extends Opengraph {

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->action( 'cpseo/vars/register_extra_replacements', 'register' );
	}

	/**
	 * Registers variable replacements for WooCommerce products.
	 */
	public function register() {
		Helper::register_var_replacement(
			'wc_price',
			[ $this, 'get_product_price' ],
			[
				'name'    => esc_html__( 'Product\'s price.', 'cpseo' ),
				'desc'    => esc_html__( 'Product\'s price of the current product', 'cpseo' ),
				'example' => $this->get_product_price(),
			]
		);

		Helper::register_var_replacement(
			'wc_sku',
			[ $this, 'get_product_sku' ],
			[
				'name'    => esc_html__( 'Product\'s SKU.', 'cpseo' ),
				'desc'    => esc_html__( 'Product\'s SKU of the current product', 'cpseo' ),
				'example' => $this->get_product_sku(),
			]
		);

		Helper::register_var_replacement(
			'wc_shortdesc',
			[ $this, 'get_short_description' ],
			[
				'name'    => esc_html__( 'Product\'s short description.', 'cpseo' ),
				'desc'    => esc_html__( 'Product\'s short description of the current product', 'cpseo' ),
				'example' => $this->get_short_description(),
			]
		);

		Helper::register_var_replacement(
			'wc_brand',
			[ $this, 'get_product_brand' ],
			[
				'name'    => esc_html__( 'Product\'s brand.', 'cpseo' ),
				'desc'    => esc_html__( 'Product\'s brand of the current product', 'cpseo' ),
				'example' => $this->get_product_brand(),
			]
		);
	}

	/**
	 * Retrieves the product price.
	 *
	 * @return string
	 */
	public function get_product_price() {
		$product = $this->get_product();
		if ( ! is_object( $product ) ) {
			return '';
		}

		if ( method_exists( $product, 'get_price' ) ) {
			return wp_strip_all_tags( wc_price( $product->get_price() ), true );
		}

		return '';
	}

	/**
	 * Retrieves the product SKU.
	 *
	 * @return string
	 */
	public function get_product_sku() {
		$product = $this->get_product();
		if ( ! is_object( $product ) ) {
			return '';
		}

		if ( method_exists( $product, 'get_sku' ) ) {
			return $product->get_sku();
		}

		return '';
	}

	/**
	 * Checks if product class has a short description method.
	 * Otherwise it returns the value of the post_excerpt from the post attribute.
	 *
	 * @param WC_Product $product The product.
	 *
	 * @return string
	 */
	public function get_short_description( $product = null ) {
		if ( is_null( $product ) ) {
			$product = $this->get_product();
		}

		if ( ! is_object( $product ) ) {
			return '';
		}

		if ( method_exists( $product, 'get_short_description' ) ) {
			return $product->get_short_description();
		}

		return $product->post->post_excerpt;
	}

	/**
	 * Retrieves the product brand.
	 *
	 * @return string
	 */
	public function get_product_brand() {
		$product = $this->get_product();
		if ( ! is_object( $product ) ) {
			return '';
		}

		$brands = $this->get_brands( $product->get_id() );
		if ( ! empty( $brands ) ) {
			return $brands[0]->name;
		}

		return '';
	}
}
