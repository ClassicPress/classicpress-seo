<?php
/**
 * The Redirections Module.
 *
 * @since      0.1.8
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\Redirections

 */

namespace ClassicPress_SEO\Redirections;

use ClassicPress_SEO\Helper;
use ClassicPress_SEO\Traits\Hooker;
use ClassicPress_SEO\Helpers\Param;
use ClassicPress_SEO\Helpers\Conditional;

/**
 * Redirections class.
 *
 * @codeCoverageIgnore
 */
class Redirections {

	use Hooker;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->load_admin();

		if ( ! is_admin() ) {
			$this->action( 'wp', 'do_redirection' );
		}

		if ( Helper::has_cap( 'redirections' ) ) {
			$this->filter( 'cpseo/admin_bar/items', 'admin_bar_items', 11 );
		}

		if ( $this->disable_auto_redirect() ) {
			remove_action( 'template_redirect', 'wp_old_slug_redirect' );
		}
	}

	/**
	 * Load redirection admin and the REST API.
	 */
	private function load_admin() {
		if ( is_admin() ) {
			$this->admin = new Admin;
		}

		if ( is_admin() || Conditional::is_rest() ) {
			new Watcher;
		}
	}

	/**
	 * Do redirection on frontend.
	 */
	public function do_redirection() {
		if ( is_customize_preview() || Conditional::is_ajax() || ! isset( $_SERVER['REQUEST_URI'] ) || empty( $_SERVER['REQUEST_URI'] ) || $this->is_script_uri_or_http_x() ) {
			return;
		}

		$redirector = new Redirector;
	}

	/**
	 * Add admin bar item.
	 *
	 * @param array $items Array of admin bar nodes.
	 * @return array
	 */
	public function admin_bar_items( $items ) {

		$items['redirections'] = [
			'id'        => 'cpseo-redirections',
			'title'     => esc_html__( 'Redirections', 'cpseo' ),
			'href'      => Helper::get_admin_url( 'redirections' ),
			'parent'    => 'cpseo',
			'meta'      => [ 'title' => esc_html__( 'Create and edit redirections', 'cpseo' ) ],
			'_priority' => 50,
		];

		$items['redirections-child'] = [
			'id'        => 'cpseo-redirections-child',
			'title'     => esc_html__( 'Manage Redirections', 'cpseo' ),
			'href'      => Helper::get_admin_url( 'redirections' ),
			'parent'    => 'cpseo-redirections',
			'meta'      => [ 'title' => esc_html__( 'Create and edit redirections', 'cpseo' ) ],
			'_priority' => 51,
		];

		$items['redirections-settings'] = [
			'id'        => 'cpseo-redirections-settings',
			'title'     => esc_html__( 'Redirection Settings', 'cpseo' ),
			'href'      => Helper::get_admin_url( 'options-general' ) . '#setting-panel-redirections',
			'parent'    => 'cpseo-redirections',
			'meta'      => [ 'title' => esc_html__( 'Redirection Settings', 'cpseo' ) ],
			'_priority' => 52,
		];

		if ( ! is_admin() ) {
			$items['redirections-redirect-me'] = [
				'id'        => 'cpseo-redirections-redirect-me',
				'title'     => esc_html__( '&raquo; Redirect this page', 'cpseo' ),
				'href'      => add_query_arg( 'url', urlencode( ltrim( Param::server( 'REQUEST_URI' ), '/' ) ), Helper::get_admin_url( 'redirections' ) ),
				'parent'    => 'cpseo-redirections',
				'meta'      => [ 'title' => esc_html__( 'Redirect the current URL', 'cpseo' ) ],
				'_priority' => 53,
			];
		}

		return $items;
	}

	/**
	 * Check if request is script URI or a http-x request.
	 *
	 * @return boolean
	 */
	private function is_script_uri_or_http_x() {
		if ( isset( $_SERVER['SCRIPT_URI'] ) && ! empty( $_SERVER['SCRIPT_URI'] ) && admin_url( 'admin-ajax.php' ) === Param::server( 'SCRIPT_URI' ) ) {
			return true;
		}

		if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( Param::server( 'HTTP_X_REQUESTED_WITH' ) ) === 'xmlhttprequest' ) {
			return true;
		}

		return false;
	}

	/**
	 * Disable Auto-Redirect.
	 *
	 * @return bool
	 */
	private function disable_auto_redirect() {
		return get_option( 'permalink_structure' ) && Helper::get_settings( 'general.cpseo_redirections_post_redirect' );
	}
}
