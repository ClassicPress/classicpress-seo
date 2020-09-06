<?php
/**
 * Register all the necessary CSS and JS.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin
 */


namespace Classic_SEO\Admin;

use Classic_SEO\Helper;
use Classic_SEO\Runner;
use Classic_SEO\Traits\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Assets class.
 *
 * @codeCoverageIgnore
 */
class Assets implements Runner {

	use Hooker;
	
	/**
	 *  Prefix for the enqueue handles.
	 */
	const PREFIX = 'cpseo-';

	/**
	 * Register hooks.
	 */
	public function hooks() {
		$this->action( 'admin_enqueue_scripts', 'register' );
		$this->action( 'admin_enqueue_scripts', 'enqueue' );
		$this->action( 'admin_enqueue_scripts', 'overwrite_wplink', 99 );
		//$this->action( 'admin_enqueue_scripts', 'load_main_styles' );
	}

	/**
	 * Register styles and scripts.
	 */
	public function register() {
		
		$js     = cpseo()->plugin_url() . 'assets/admin/js/';
		$css    = cpseo()->plugin_url() . 'assets/admin/css/';
		$vendor = cpseo()->plugin_url() . 'assets/vendor/';

		// Styles.
		wp_register_style( self::PREFIX . 'common', $css . 'common.css', null, cpseo()->version );
		wp_register_style( self::PREFIX . 'cmb2', $css . 'cmb2.css', null, cpseo()->version );
		wp_register_style( self::PREFIX . 'dashboard', $css . 'dashboard.css', [ 'cpseo-common' ], cpseo()->version );
		wp_register_style( self::PREFIX . 'admin', $css . 'cpseo.css', null, cpseo()->version );
		wp_register_style( 'select2-rm', $vendor . 'select2/select2.min.css', null, '4.0.6-rc.1' );

		// Scripts.
		wp_register_script( 'clipboard', $vendor . 'clipboard.min.js', null, '2.0.0', true );
		wp_register_script( 'validate', $vendor . 'jquery.validate.min.js', [ 'jquery' ], '1.19.0', true );
		wp_register_script( self::PREFIX . 'validate', $js . 'validate.js', [ 'jquery' ], CPSEO_VERSION, true );
		wp_register_script( self::PREFIX . 'common', $js . 'common.js', [ 'jquery', 'validate' ], CPSEO_VERSION, true );	
		wp_register_script( self::PREFIX . 'dashboard', $js . 'dashboard.js', [ 'jquery', 'clipboard', 'validate' ], CPSEO_VERSION, true );
		wp_register_script( 'select2-rm', $vendor . 'select2/select2.min.js', null, '4.0.6-rc.1', true );

		Helper::add_json( 'api', array( 'root'  => esc_url_raw( get_rest_url() ), 'nonce' => ( wp_installing() && ! is_multisite() ) ? '' : wp_create_nonce( 'wp_rest' ) ) );
		Helper::add_json(
			'validationl10n',
			[
				'regexErrorDefault'    => __( 'Please use the correct format.', 'cpseo' ),
				'requiredErrorDefault' => __( 'This field is required.', 'cpseo' ),
				'emailErrorDefault'    => __( 'Please enter a valid email address.', 'cpseo' ),
				'urlErrorDefault'      => __( 'Please enter a valid URL.', 'cpseo' ),
			]
		);

		/**
		 * Allow other plugins to register/deregister admin styles or scripts after plugin assets.
		 */
		$this->do_action( 'admin/register_scripts' );
	}
	
	
	public function load_main_styles() {
		wp_register_style( self::PREFIX . 'css-admin', CPSEO_PLUGIN_URL . 'assets/admin/css/cpseo.css', null, CPSEO_VERSION );
		wp_enqueue_style( self::PREFIX . 'css-admin' );
	}
	

	/**
	 * Enqueue styles and scripts.
	 */
	public function enqueue() {
		$screen = get_current_screen();

		// Our screens only.
		if ( ! in_array( $screen->taxonomy, Helper::get_allowed_taxonomies(), true ) && ! in_array( $screen->id, $this->get_admin_screen_ids(), true ) ) {
			return;
		}

		Helper::add_json( 'showScore', Helper::is_score_enabled() );

		/**
		 * Allow other plugins to enqueue/dequeue admin styles or scripts after plugin assets.
		 */
		$this->do_action( 'admin/enqueue_scripts' );
	}

	/**
	 * Overwrite wplink script file.
	 * Classic SEO adds new options in the link popup when editing a post.
	 */
	public function overwrite_wplink() {

		wp_deregister_script( 'wplink' );
		wp_register_script( 'wplink', CPSEO_PLUGIN_URL . 'assets/admin/js/wplink.js', array ( 'jquery', 'wpdialogs' ), null, true );

		wp_localize_script(
			'wplink',
			'wpLinkL10n',
			[
				'title'             => esc_html__( 'Insert/edit link', 'cpseo' ),
				'update'            => esc_html__( 'Update', 'cpseo' ),
				'save'              => esc_html__( 'Add Link', 'cpseo' ),
				'noTitle'           => esc_html__( '(no title)', 'cpseo' ),
				'noMatchesFound'    => esc_html__( 'No matches found.', 'cpseo' ),
				'linkSelected'      => esc_html__( 'Link selected.', 'cpseo' ),
				'linkInserted'      => esc_html__( 'Link inserted.', 'cpseo' ),
				'relCheckbox'       => __( 'Add <code>rel="nofollow"</code>', 'cpseo' ),
				'sponsoredCheckbox' => __( 'Add <code>rel="sponsored"</code>', 'cpseo' ),
				'linkTitle'         => esc_html__( 'Link Title', 'cpseo' ),
			]
		);
	}

	/**
	 * Enqueues styles.
	 *
	 * @param string $style The name of the style to enqueue.
	 */
	public function enqueue_style( $style ) {
		wp_enqueue_style( self::PREFIX . $style );
	}

	/**
	 * Enqueues scripts.
	 *
	 * @param string $script The name of the script to enqueue.
	 */
	public function enqueue_script( $script ) {
		wp_enqueue_script( self::PREFIX . $script );
	}
	
	/**
	 * Get admin screen ids.
	 *
	 * @return array
	 */
	private function get_admin_screen_ids() {
		$pages = [
			'toplevel_page_cpseo',
			'cpseo_page_cpseo-role-manager',
			'cpseo_page_cpseo-seo-analysis',
			'cpseo_page_cpseo-404-monitor',
			'cpseo_page_cpseo-redirections',
			'cpseo_page_cpseo-link-builder',
			'cpseo_page_cpseo-import-export',
			'cpseo_page_cpseo-help',
			'user-edit',
			'profile',
		];

		return array_merge( $pages, Helper::get_allowed_post_types() );
	}
	
}
