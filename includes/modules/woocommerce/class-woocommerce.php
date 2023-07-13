<?php
/**
 * The WooCommerce Module
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\WooCommerce

 */

namespace Classic_SEO\WooCommerce;

use Classic_SEO\Helper;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Helpers\Str;
use Classic_SEO\Helpers\Param;

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce class.
 */
#[\AllowDynamicProperties]
class WooCommerce extends WC_Vars {

	use Hooker;

	/**
	 * Hold product categories.
	 *
	 * @var array
	 */
	private $categories;

	/**
	 * Hold product.
	 *
	 * @var WC_Product
	 */
	private $product = null;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->remove_product_base  = Helper::get_settings( 'general.cpseo_wc_remove_product_base' );
		$this->remove_category_base = Helper::get_settings( 'general.cpseo_wc_remove_category_base' );
		$this->remove_parent_slugs  = Helper::get_settings( 'general.cpseo_wc_remove_category_parent_slugs' );

		if ( is_admin() ) {
			new Admin;
		}

		$this->integrations();

		if ( $this->remove_product_base ) {
			$this->filter( 'post_type_link', 'product_post_type_link', 1, 2 );
		}

		if ( $this->remove_category_base || $this->remove_parent_slugs ) {
			$this->filter( 'term_link', 'product_term_link', 1, 3 );
			add_action( 'created_product_cat', 'Classic_SEO\\Helper::schedule_flush_rewrite' );
			add_action( 'delete_product_cat', 'Classic_SEO\\Helper::schedule_flush_rewrite' );
			add_action( 'edited_product_cat', 'Classic_SEO\\Helper::schedule_flush_rewrite' );
		}

		if ( $this->remove_product_base || $this->remove_category_base ) {
			new Product_Redirection;
		}

		$this->filter( 'rewrite_rules_array', 'add_rewrite_rules', 99 );
		parent::__construct();
	}

	/**
	 * Initialize integrations.
	 */
	public function integrations() {
		if ( is_admin() ) {
			return;
		}
		// Permalink Manager.
		if ( $this->remove_product_base || $this->remove_category_base || $this->remove_parent_slugs ) {
			$this->action( 'request', 'request' );
		}

		if ( Helper::get_settings( 'general.cpseo_wc_remove_generator' ) ) {
			remove_action( 'get_the_generator_html', 'wc_generator_tag', 10 );
			remove_action( 'get_the_generator_xhtml', 'wc_generator_tag', 10 );
		}

		$this->sitemap();
		$this->opengraph();
		$this->filter( 'cpseo/frontend/description', 'metadesc' );
		$this->filter( 'cpseo/frontend/robots', 'robots' );
	}

	/**
	 * Replace request if product found.
	 *
	 * @param array $request Current request.
	 *
	 * @return array
	 */
	public function request( $request ) {
		global $wp, $wpdb;
		$url = $wp->request;

		if ( empty( $url ) ) {
			return $request;
		}

		$replace = [];
		$url     = explode( '/', $url );
		$slug    = array_pop( $url );

		if ( 'feed' === $slug ) {
			return $request;
		}

		if ( 'amp' === $slug ) {
			$replace['amp'] = $slug;
			$slug           = array_pop( $url );
		}

		if ( 0 === strpos( $slug, 'comment-page-' ) ) {
			$replace['cpage'] = substr( $slug, strlen( 'comment-page-' ) );
			$slug             = array_pop( $url );
		}

		$query = "SELECT COUNT(ID) as count_id FROM {$wpdb->posts} WHERE post_name = %s AND post_type = %s";
		$num   = intval( $wpdb->get_var( $wpdb->prepare( $query, [ $slug, 'product' ] ) ) ); // phpcs:ignore
		if ( $num > 0 ) {
			$replace['page']      = '';
			$replace['name']      = $slug;
			$replace['product']   = $slug;
			$replace['post_type'] = 'product';

			return $replace;
		}

		return $request;
	}

	/**
	 * Replace product permalink according to settings.
	 *
	 * @param string  $permalink The existing permalink URL.
	 * @param WP_Post $post WP_Post object.
	 *
	 * @return string
	 */
	public function product_post_type_link( $permalink, $post ) {
		if ( $this->can_change_link( 'product', $post->post_type ) ) {
			return $permalink;
		}

		$permalink_structure = wc_get_permalink_structure();
		$product_base        = $permalink_structure['product_rewrite_slug'];
		$product_base        = explode( '/', ltrim( $product_base, '/' ) );

		$link = $permalink;
		foreach ( $product_base as $remove ) {
			if ( '%product_cat%' === $remove ) {
				continue;
			}
			$link = preg_replace( "#{$remove}/#i", '', $link, 1 );
		}

		return $link;
	}

	/**
	 * Replace category permalink according to settings.
	 *
	 * @param string $link     Term link URL.
	 * @param object $term     Term object.
	 * @param string $taxonomy Taxonomy slug.
	 *
	 * @return string
	 */
	public function product_term_link( $link, $term, $taxonomy ) {
		if ( $this->can_change_link( 'product_cat', $taxonomy ) ) {
			return $link;
		}

		$permalink_structure  = wc_get_permalink_structure();
		$category_base        = trailingslashit( $permalink_structure['category_rewrite_slug'] );
		$is_language_switcher = ( class_exists( 'Sitepress' ) && strpos( $original_link, 'lang=' ) );

		if ( $this->remove_category_base ) {
			$link          = str_replace( $category_base, '', $link );
			$category_base = '';
		}

		if ( $this->remove_parent_slugs && ! $is_language_switcher ) {
			$link = home_url( trailingslashit( $category_base . $term->slug ) );
		}

		return $link;
	}

	/**
	 * Can change link
	 *
	 * @param string $check   Check string.
	 * @param string $against Against this.
	 *
	 * @return bool
	 */
	private function can_change_link( $check, $against ) {
		return $check !== $against || ! get_option( 'permalink_structure' );
	}

	/**
	 * Change robots for WooCommerce pages according to settings
	 *
	 * @param array $robots Array of robots to sanitize.
	 *
	 * @return array Modified robots.
	 */
	public function robots( $robots ) {

		// Early Bail if current page is Woocommerce OnePage Checkout.
		if ( function_exists( 'is_wcopc_checkout' ) && is_wcopc_checkout() ) {
			return $robots;
		}

		if ( is_cart() || is_checkout() || is_account_page() ) {
			remove_action( 'wp_head', 'wc_page_noindex' );
			return [
				'index'  => 'noindex',
				'follow' => 'follow',
			];
		}

		return $robots;
	}

	/**
	 * Returns the meta description. Checks which value should be used when the given meta description is empty.
	 *
	 * It will use the short_description if that one is set. Otherwise it will use the full
	 * product description limited to 156 characters. If everything is empty, it will return an empty string.
	 *
	 * @param string $metadesc The meta description to check.
	 *
	 * @return string The meta description.
	 */
	public function metadesc( $metadesc ) {
		if ( '' !== $metadesc || ! is_singular( 'product' ) ) {
			return $metadesc;
		}

		$product = $this->get_product_by_id( get_the_id() );
		if ( ! is_object( $product ) ) {
			return '';
		}

		$short_desc = $this->get_short_description( $product );
		if ( '' !== $short_desc ) {
			return $short_desc;
		}

		$long_desc = $this->get_long_description( $product );
		return '' !== $long_desc ? wp_html_excerpt( $long_desc, 156 ) : '';
	}

	/**
	 * Returns the product for given product_id.
	 *
	 * @param int $product_id The id to get the product for.
	 *
	 * @return null|WC_Product
	 */
	protected function get_product_by_id( $product_id ) {
		if ( function_exists( 'wc_get_product' ) ) {
			return wc_get_product( $product_id );
		}

		if ( function_exists( 'get_product' ) ) {
			return get_product( $product_id );
		}

		return null;
	}

	/**
	 * Add rewrite rules for wp.
	 *
	 * @param array $rules The compiled array of rewrite rules.
	 *
	 * @return array
	 */
	public function add_rewrite_rules( $rules ) {
		global $wp_rewrite;
		wp_cache_flush();

		$permalink_structure = wc_get_permalink_structure();
		$category_base       = $this->remove_category_base ? '' : $permalink_structure['category_rewrite_slug'];
		$use_parent_slug     = Str::contains( '%product_cat%', $permalink_structure['product_rewrite_slug'] );

		$product_rules  = [];
		$category_rules = [];
		foreach ( $this->get_categories() as $category ) {
			$category_path = $this->get_category_fullpath( $category );
			$category_slug = $this->remove_parent_slugs ? $category['slug'] : $category_path;

			$category_rules[ $category_base . $category_slug . '/?$' ]                                    = 'index.php?product_cat=' . $category['slug'];
			$category_rules[ $category_base . $category_slug . '/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$' ] = 'index.php?product_cat=' . $category['slug'] . '&feed=$matches[1]';
			$category_rules[ $category_base . $category_slug . '/' . $wp_rewrite->pagination_base . '/?([0-9]{1,})/?$' ] = 'index.php?product_cat=' . $category['slug'] . '&paged=$matches[1]';

			if ( $this->remove_product_base && $use_parent_slug ) {
				$product_rules[ $category_path . '/([^/]+)/?$' ] = 'index.php?product=$matches[1]';
				$product_rules[ $category_path . '/([^/]+)/' . $wp_rewrite->comments_pagination_base . '-([0-9]{1,})/?$' ] = 'index.php?product=$matches[1]&cpage=$matches[2]';
			}
		}

		$rules = empty( $rules ) ? [] : $rules;
		return $category_rules + $product_rules + $rules;
	}

	/**
	 * Returns categories array.
	 *
	 * ['category id' => ['slug' => 'category slug', 'parent' => 'parent category id']]
	 *
	 * @return array
	 */
	protected function get_categories() {
		if ( is_null( $this->categories ) ) {
			$categories = get_categories(
				[
					'taxonomy'   => 'product_cat',
					'hide_empty' => false,
				]
			);

			$slugs = [];
			foreach ( $categories as $category ) {
				$slugs[ $category->term_id ] = [
					'parent' => $category->parent,
					'slug'   => $category->slug,
				];
			}

			$this->categories = $slugs;
		}

		return $this->categories;
	}

	/**
	 * Recursively builds category full path.
	 *
	 * @param object $category Term object.
	 *
	 * @return string
	 */
	protected function get_category_fullpath( $category ) {
		$categories = $this->get_categories();
		$parent     = $category['parent'];

		if ( $parent > 0 && array_key_exists( $parent, $categories ) ) {
			return $this->get_category_fullpath( $categories[ $parent ] ) . '/' . $category['slug'];
		}

		return $category['slug'];
	}

	/**
	 * Checks if product class has a description method.
	 * Otherwise it returns the value of the post_content.
	 *
	 * @param WC_Product $product The product.
	 *
	 * @return string
	 */
	protected function get_long_description( $product ) {
		if ( method_exists( $product, 'get_description' ) ) {
			return $product->get_description();
		}

		return $product->post->post_content;
	}

	/**
	 * Returns the product object when the current page is the product page.
	 *
	 * @return null|WC_Product
	 */
	public function get_product() {
		if ( ! is_null( $this->product ) ) {
			return $this->product;
		}

		$product_id    = Param::get( 'post', get_queried_object_id(), FILTER_VALIDATE_INT );
		$this->product = (
			! function_exists( 'wc_get_product' ) ||
			! $product_id ||
			(
				! is_admin() &&
				! is_singular( 'product' )
			)
		) ? null : wc_get_product( $product_id );

		return $this->product;
	}

	/**
	 * Returns the array of brand taxonomy.
	 *
	 * @param int $product_id The id to get the product brands for.
	 *
	 * @return bool|array
	 */
	public static function get_brands( $product_id ) {
		$taxonomy = Helper::get_settings( 'general.cpseo_product_brand' );
		if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
			return false;
		}

		$brands = wp_get_post_terms( $product_id, $taxonomy );
		return empty( $brands ) || is_wp_error( $brands ) ? false : $brands;
	}
}
