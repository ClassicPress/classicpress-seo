<?php
/**
 * The Capability Manager.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Role_Manager

 */

namespace Classic_SEO\Role_Manager;

use Classic_SEO\Helper;
use Classic_SEO\Module;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Admin\Page;
use Classic_SEO\Helpers\WordPress;

defined( 'ABSPATH' ) || exit;

/**
 * Capability_Manager class.
 */
class Capability_Manager {

	use Hooker, WordPress;

	/**
	 * Registered capabilities.
	 *
	 * @var array
	 */
	protected $capabilities = [];

	/**
	 * Main instance
	 *
	 * Ensure only one instance is loaded or can be loaded.
	 *
	 * @return Capability_Manager
	 */
	public static function get() {
		static $instance;

		if ( is_null( $instance ) && ! ( $instance instanceof Capability_Manager ) ) {
			$instance = new Capability_Manager;
			$instance->set_capabilities();
		}

		return $instance;
	}

	/**
	 * Set default capabilities.
	 *
	 * @codeCoverageIgnore
	 */
	public function set_capabilities() {
		$this->register( 'cpseo_titles', esc_html__( 'Titles & Meta Settings', 'cpseo' ) );
		$this->register( 'cpseo_general', esc_html__( 'General Settings', 'cpseo' ) );
		$this->register( 'cpseo_sitemap', esc_html__( 'XML Sitemap Settings', 'cpseo' ) );
		$this->register( 'cpseo_404_monitor', esc_html__( '404 Monitor Log', 'cpseo' ) );
		$this->register( 'cpseo_link_builder', esc_html__( 'Link Builder', 'cpseo' ) );
		$this->register( 'cpseo_redirections', esc_html__( 'Redirections', 'cpseo' ) );
		$this->register( 'cpseo_role_manager', esc_html__( 'Role Manager', 'cpseo' ) );
		$this->register( 'cpseo_onpage_analysis', esc_html__( 'On-Page Analysis', 'cpseo' ) );
		$this->register( 'cpseo_onpage_general', esc_html__( 'On-Page General Settings', 'cpseo' ) );
		$this->register( 'cpseo_onpage_advanced', esc_html__( 'On-Page Advanced Settings', 'cpseo' ) );
		$this->register( 'cpseo_onpage_snippet', esc_html__( 'On-Page Rich Snippet Settings', 'cpseo' ) );
		$this->register( 'cpseo_onpage_social', esc_html__( 'On-Page Social Settings', 'cpseo' ) );
		$this->register( 'cpseo_admin_bar', esc_html__( 'Top Admin Bar', 'cpseo' ) );
	}

	/**
	 * Registers a capability.
	 *
	 * @param string $capability Capability to register.
	 * @param string $title      Capability human title.
	 */
	public function register( $capability, $title ) {
		$this->capabilities[ $capability ] = $title;
	}

	/**
	 * Returns the list of registered capabilitities.
	 *
	 * @return string[] Registered capabilities.
	 */
	public function get_capabilities() {
		return array_keys( $this->capabilities );
	}

	/**
	 * Add capabilities on install.
	 */
	public function create_capabilities() {
		foreach ( Capability_Manager::get_roles() as $slug => $role ) {
			$role = get_role( $slug );
			if ( ! $role ) {
				continue;
			}

			$this->loop_capabilities( $this->get_default_capabilities_by_role( $slug ), 'add_cap', $role );
		}
	}

	/**
	 * Remove capabilities on uninstall.
	 */
	public function remove_capabilities() {
		$capabilities = $this->get_capabilities();
		foreach ( WordPress::get_roles() as $slug => $role ) {
			$role = get_role( $slug );
			if ( ! $role ) {
				continue;
			}

			$this->loop_capabilities( $capabilities, 'remove_cap', $role );
		}
	}

	/**
	 * Loop capabilities and perform action.
	 *
	 * @param array  $caps    Capabilities.
	 * @param string $perform Action to perform.
	 * @param object $role    Role object.
	 */
	private function loop_capabilities( $caps, $perform, $role ) {
		foreach ( $caps as $cap ) {
			$role->$perform( $cap );
		}
	}

	/**
	 * Get default capabilities by roles.
	 *
	 * @param  string $role Capabilities for this role.
	 * @return array
	 */
	private function get_default_capabilities_by_role( $role ) {

		if ( 'administrator' === $role ) {
			return $this->get_capabilities();
		}

		if ( 'editor' === $role ) {
			return [
				'cpseo_onpage_analysis',
				'cpseo_onpage_general',
				'cpseo_onpage_snippet',
				'cpseo_onpage_social',
			];
		}

		if ( 'author' === $role ) {
			return [
				'cpseo_onpage_analysis',
				'cpseo_onpage_general',
				'cpseo_onpage_snippet',
				'cpseo_onpage_social',
			];
		}

		return [];
	}
}
