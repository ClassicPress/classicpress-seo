<?php
/**
 * The Redirections Module.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Redirections

 */

namespace Classic_SEO\Redirections;

use Classic_SEO\Helper;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Helpers\Param;
use Classic_SEO\Helpers\Conditional;

/**
 * Redirections class.
 *
 * @codeCoverageIgnore
 */
#[\AllowDynamicProperties]
class Redirections {

	use Hooker, Conditional;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->load_admin();

		if ( ! is_admin() ) {
			$this->action( 'wp', 'do_redirection' );
		}

		if ( Helper::has_cap( 'redirections' ) ) {
			$this->action( 'cpseo/admin_bar/items', 'admin_bar_items', 11 );
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

		if ( is_admin() || Redirections::is_rest() ) {
			new Watcher;
		}
	}

	/**
	 * Do redirection on frontend.
	 */
	public function do_redirection() {
		if ( is_customize_preview() || Redirections::is_ajax() || ! isset( $_SERVER['REQUEST_URI'] ) || empty( $_SERVER['REQUEST_URI'] ) || $this->is_script_uri_or_http_x() ) {
			return;
		}

		$redirector = new Redirector;
	}

	/**
	 * Add admin bar item.
	 *
	 * @param Admin_Bar_Menu $menu Menu class instance.
	 */
	public function admin_bar_items( $menu ) {
		$menu->add_sub_menu(
			'redirections',
			[
				'title'    => esc_html__( 'Redirections', 'cpseo' ),
				'href'     => Helper::get_admin_url( 'redirections' ),
				'meta'     => [ 'title' => esc_html__( 'Create and edit redirections', 'cpseo' ) ],
				'priority' => 50,
			]
		);

		$menu->add_sub_menu(
			'redirections-edit',
			[
				'title' => esc_html__( 'Manage Redirections', 'cpseo' ),
				'href'  => Helper::get_admin_url( 'redirections' ),
				'meta'  => [ 'title' => esc_html__( 'Create and edit redirections', 'cpseo' ) ],
			],
			'redirections'
		);

		$menu->add_sub_menu(
			'redirections-settings',
			[
				'title' => esc_html__( 'Redirection Settings', 'cpseo' ),
				'href'  => Helper::get_admin_url( 'options-general' ) . '#setting-panel-redirections',
				'meta'  => [ 'title' => esc_html__( 'Redirection Settings', 'cpseo' ) ],
			],
			'redirections'
		);

		if ( ! is_admin() ) {
			$menu->add_sub_menu(
				'redirections-redirect-me',
				[
					'title' => esc_html__( '&raquo; Redirect this page', 'cpseo' ),
					'href'  => add_query_arg( 'url', urlencode( ltrim( Param::server( 'REQUEST_URI' ), '/' ) ), Helper::get_admin_url( 'redirections' ) ),
					'meta'  => [ 'title' => esc_html__( 'Redirect the current URL', 'cpseo' ) ],
				],
				'redirections'
			);
		}
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
