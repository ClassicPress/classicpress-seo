<?php
/**
 * The WooCommerce Module
 *
 * @since      1.0.32
 * @package    Classic_SEO
 * @subpackage Classic_SEO\WooCommerce

 */

namespace Classic_SEO\WooCommerce;

use Classic_SEO\Helper;
use Classic_SEO\Traits\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce class.
 */
class Product_Redirection {

	use Hooker;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		if ( Helper::is_module_active( 'redirections' ) ) {
			$this->filter( 'cpseo/redirection/pre_search', 'pre_redirection', 10, 3 );
			return;
		}

		$this->action( 'wp', 'redirect' );
	}

	/**
	 * Pre-filter the redirection.
	 *
	 * @param string $check    Check.
	 * @param string $uri      Current URL.
	 * @param string $full_uri Full URL.
	 *
	 * @return string|array
	 */
	public function pre_redirection( $check, $uri, $full_uri ) {
		if ( $new_link = $this->get_redirection_url( $uri ) ) { // phpcs:ignore
			return [
				'url_to'      => $new_link,
				'header_code' => 301,
			];
		}

		return $check;
	}

	/**
	 * Redirect product with base to the new link.
	 */
	public function redirect() {
		global $wp;

		if ( $link = $this->get_redirection_url( $wp->request ) ) { // phpcs:ignore
			wp_redirect( $link, 301 );
			exit;
		}
	}

	/**
	 * Get Product URL.
	 *
	 * @param string $uri Current URL.
	 *
	 * @return string Modified URL
	 */
	private function get_redirection_url( $uri ) {
		if ( ! $this->can_redirect() ) {
			return false;
		}

		$permalink_structure = wc_get_permalink_structure();
		$base                = is_product_category() ? $permalink_structure['category_base'] : $permalink_structure['product_base'];

		$base     = explode( '/', ltrim( $base, '/' ) );
		$new_link = $uri;
		
		// Early Bail if new_link length is less then the base.
		if ( count( explode( '/', $new_link ) ) <= count( $base ) ) {
			return false;
		}

		foreach ( $base as $remove ) {
			if ( '%product_cat%' === $remove ) {
				continue;
			}
			$new_link = preg_replace( "#{$remove}/#i", '', $new_link, 1 );
		}

		return $new_link === $uri ? false : trailingslashit( home_url( $new_link ) );
	}

	/**
	 * Can redirect to the new product link.
	 *
	 * @return bool
	 */
	private function can_redirect() {
		if (
			$this->do_filter( 'woocommerce/product_redirection', true ) &&
			( ( Helper::get_settings( 'general.cpseo_wc_remove_product_base' ) && is_product() ) ||
			( Helper::get_settings( 'general.cpseo_wc_remove_category_base' ) && is_product_category() ) )
		) {
			return true;
		}

		return false;
	}
}
