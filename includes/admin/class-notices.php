<?php
/**
 * The admin notices.
 *
 * @since      0.1.8
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\Admin
 */

namespace ClassicPress_SEO\Admin;

use ClassicPress_SEO\Runner;
use ClassicPress_SEO\Helper;
use ClassicPress_SEO\Traits\Ajax;
use ClassicPress_SEO\Traits\Hooker;
use ClassicPress_SEO\Helpers\Param;

defined( 'ABSPATH' ) || exit;

/**
 * Notices class.
 */
class Notices implements Runner {

	use Hooker, Ajax;

	/**
	 * Register hooks.
	 */
	public function hooks() {
		$this->action( 'admin_init', 'notices' );
		$this->action( 'wp_helpers_notification_dismissed', 'notice_dismissible' );
	}

	/**
	 * Run all notices routine.
	 */
	public function notices() {
		$this->is_plugin_configured();
		$this->new_post_type();
	}

	/**
	 * Set known post type after notice dismissal.
	 *
	 * @param string $notification_id Notification id.
	 */
	public function notice_dismissible( $notification_id ) {
		if ( 'new_post_type' !== $notification_id ) {
			return;
		}

		$current = get_post_types( [ 'public' => true ] );
		update_option( 'cpseo_known_post_types', $current );

		if ( Helper::is_module_active( 'sitemap' ) ) {
			\ClassicPress_SEO\Sitemap\Cache::invalidate_storage();
		}
	}

	/**
	 * If plugin configuration not done.
	 */
	private function is_plugin_configured() {
		if ( 'mts-install-plugins' === Param::get( 'page' ) ) {
			return;
		}

		if ( cpseo()->notification->get_notification_by_id( 'plugin_not_setup' ) && ! Helper::is_configured() ) {
			$message = printf('<b>Warning!</b> You haven\'t finished setting up your ClassicPress SEO plugin.');
			Helper::add_notification(
				$message,
				[
					'type' => 'warning',
					'id'   => 'plugin_not_setup',
				]
			);
		}
	}

	/**
	 * Add notification if a new post type is detected.
	 */
	private function new_post_type() {
		$known   = get_option( 'cpseo_known_post_types', [] );
		$current = Helper::get_accessible_post_types();
		$new     = array_diff( $current, $known );

		if ( empty( $new ) ) {
			return;
		}

		$list = implode( ', ', $new );
		/* translators: post names */
		$message = $this->do_filter( 'admin/notice/new_post_type', __( 'We detected new post type(s) (%1$s). Check the settings for <a href="%2$s">Titles &amp; Meta page</a>.', 'cpseo' ) );
		$message = sprintf( wp_kses_post( $message ), $list, Helper::get_admin_url( 'options-titles#setting-panel-post-type-' . key( $new ) ), Helper::get_admin_url( 'options-sitemap#setting-panel-sitemap-post-type-' . key( $new ) ) );
		Helper::add_notification(
			$message,
			[
				'type' => 'info',
				'id'   => 'new_post_type',
			]
		);
	}
}
