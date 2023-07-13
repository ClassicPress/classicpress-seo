<?php
/**
 * The Role Manager Module.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Role_Manager

 */

namespace Classic_SEO\Role_Manager;

use Classic_SEO\Helper;
use Classic_SEO\Module\Base;
use Classic_SEO\Admin\Page;
use Classic_SEO\Helpers\WordPress;

defined( 'ABSPATH' ) || exit;

/**
 * Role_Manager class.
 */
#[\AllowDynamicProperties]
class Role_Manager extends Base {

	use Wordpress;

	/**
	 * The Constructor.
	 */
	public function __construct() {

		$directory = dirname( __FILE__ );
		$this->config([
			'id'        => 'role-manager',
			'directory' => $directory,
			'help'      => [
				'title' => esc_html__( 'Role Manager', 'cpseo' ),
				'view'  => $directory . '/views/help.php',
			],
		]);
		parent::__construct();

		$this->action( 'cmb2_admin_init', 'register_form' );
		add_filter( 'cmb2_override_option_get_cpseo-role-manager', [ '\Classic_SEO\Helper', 'get_roles_capabilities' ] );
		$this->action( 'admin_post_cpseo_save_capabilities', 'save_capabilities' );

		if ( $this->page->is_current_page() ) {
			add_action( 'admin_enqueue_scripts', [ 'CMB2_hookup', 'enqueue_cmb_css' ], 25 );
		}

		// Members plugin integration.
		if ( \function_exists( 'members_plugin' ) ) {
			new Members;
		}

		// User Role Editor plugin integration.
		if ( defined( 'URE_PLUGIN_URL' ) ) {
			new User_Role_Editor;
		}
	}

	/**
	 * Register admin page.
	 */
	public function register_admin_page() {
		$uri = untrailingslashit( plugin_dir_url( __FILE__ ) );

		$this->page = new Page( 'cpseo-role-manager', esc_html__( 'Role Manager', 'cpseo' ), [
			'position' => 11,
			'parent'   => 'cpseo',
			'render'   => $this->directory . '/views/main.php',
			'classes'  => [ 'cpseo-page' ],
			'assets'   => [
				'styles' => [
					'cpseo-common'       => '',
					'cpseo-cmb2'         => '',
					'cpseo-role-manager' => $uri . '/assets/role-manager.css',
				],
			],
		]);
	}

	/**
	 * Register form for Add New Record.
	 */
	public function register_form() {

		$cmb = new_cmb2_box( [
			'id'           => 'cpseo-role-manager',
			'object_types' => [ 'options-page' ],
			'option_key'   => 'cpseo-role-manager',
			'hookup'       => false,
			'save_fields'  => false,
		]);

		foreach ( Role_Manager::get_roles() as $role => $label ) {
			$cmb->add_field([
				'id'                => esc_attr( $role ),
				'type'              => 'multicheck_inline',
				'name'              => translate_user_role( $label ),
				'options'           => Helper::get_capabilities(),
				'select_all_button' => false,
				'classes'           => 'cmb-big-labels',
			]);
		}
	}

	/**
	 * Save capabilities form submit handler.
	 */
	public function save_capabilities() {

		// If no form submission, bail!
		if ( empty( $_POST ) ) {
			return false;
		}

		check_admin_referer( 'cpseo-save-capabilities', 'security' );

		if ( ! Helper::has_cap( 'role_manager' ) ) {
			Helper::add_notification( esc_html__( 'You are not authorized to perform this action.', 'cpseo' ), [ 'type' => 'error' ] );
			wp_safe_redirect( Helper::get_admin_url( 'role-manager' ) );
			exit;
		}

		$cmb = cmb2_get_metabox( 'cpseo-role-manager' );
		Helper::set_capabilities( $cmb->get_sanitized_values( $_POST ) );

		wp_safe_redirect( Helper::get_admin_url( 'role-manager' ) );
		exit;
	}
}
