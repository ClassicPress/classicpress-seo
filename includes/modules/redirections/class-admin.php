<?php
/**
 * The Redirections Module
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Redirections

 */

namespace Classic_SEO\Redirections;

use CMB2_hookup;
use Classic_SEO\Helper;
use Classic_SEO\Module\Base;
use Classic_SEO\Traits\Ajax;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Admin\Admin_Helper;
use Classic_SEO\Admin\Page;
use Classic_SEO\Helpers\Arr;
use Classic_SEO\Helpers\Str;
use Classic_SEO\Helpers\Param;
use Classic_SEO\Helpers\WordPress;
use Classic_SEO\Helpers\Conditional;

/**
 * Admin class.
 */
#[\AllowDynamicProperties]
class Admin extends Base {

	use Ajax, Hooker, Conditional, WordPress;

	/**
	 * The Constructor.
	 *
	 * @codeCoverageIgnore
	 */
	public function __construct() {
		$directory = dirname( __FILE__ );
		$this->config([
			'id'             => 'redirect',
			'directory'      => $directory,
			'table'          => 'Classic_SEO\Redirections\Table',
			'help'           => [
				'title' => esc_html__( 'Redirections', 'cpseo' ),
				'view'  => $directory . '/views/help.php',
			],
			'screen_options' => [
				'id'      => 'cpseo_redirections_per_page',
				'default' => 100,
			],
		]);
		parent::__construct();

		$this->ajax_hooks();
		$this->load_metabox();

		if ( Helper::has_cap( 'redirections' ) ) {
			$this->action( 'cpseo/dashboard/widget', 'dashboard_widget', 12 );
			$this->filter( 'cpseo/settings/general', 'add_settings' );
		}

		if ( $this->page->is_current_page() || 'cpseo_save_redirections' === Param::post( 'action' ) ) {
			$this->form = new Form;
			$this->form->hooks();
		}

		if ( $this->page->is_current_page() ) {
			new Export;
			$this->action( 'init', 'init' );
			add_action( 'admin_enqueue_scripts', [ 'CMB2_hookup', 'enqueue_cmb_css' ] );
			Helper::add_json( 'maintenanceMode', esc_html__( 'Maintenance Code', 'cpseo' ) );
			Helper::add_json( 'emptyError', __( 'This field must not be empty.', 'cpseo' ) );
		}

		add_action( 'cpseo/redirection/clean_trashed', 'Classic_SEO\\Redirections\\DB::periodic_clean_trash' );
	}

	/**
	 * Load metabox.
	 */
	private function load_metabox() {
		if ( Admin_Helper::is_post_edit() || Admin_Helper::is_term_edit() ) {
			new Metabox;
		}
	}

	/**
	 * Hooks for ajax.
	 */
	private function ajax_hooks() {
		if ( ! Admin::is_ajax() ) {
			return;
		}

		$this->ajax( 'redirection_delete', 'handle_ajax' );
		$this->ajax( 'redirection_activate', 'handle_ajax' );
		$this->ajax( 'redirection_deactivate', 'handle_ajax' );
		$this->ajax( 'redirection_trash', 'handle_ajax' );
		$this->ajax( 'redirection_restore', 'handle_ajax' );
	}

	/**
	 * Register admin page.
	 */
	public function register_admin_page() {

		$dir = $this->directory . '/views/';
		$uri = untrailingslashit( plugin_dir_url( __FILE__ ) );

		$this->page = new Page( 'cpseo-redirections', esc_html__( 'Redirections', 'cpseo' ), [
			'position'   => 12,
			'parent'     => 'cpseo',
			'capability' => 'cpseo_redirections',
			'render'     => $dir . 'main.php',
			'classes'    => [ 'cpseo-page' ],
			'assets'     => [
				'styles'  => [
					'cpseo-common'       => '',
					'cpseo-cmb2'         => '',
					'cpseo-redirections' => $uri . '/assets/redirections.css',
				],
				'scripts' => [
					'cpseo-common'       => '',
					'cpseo-redirections' => $uri . '/assets/redirections.js',
				],
			],
		]);
	}

	/**
	 * Add module settings in the General Options panel.
	 *
	 * @param  array $tabs Array of option panel tabs.
	 * @return array
	 */
	public function add_settings( $tabs ) {

		/**
		 * Allow developers to change number of redirections to process at once.
		 *
		 * @param int $number
		 */
		Helper::add_json( 'redirectionPastedContent', $this->do_filter( 'redirections/pastedContent', 100 ) );

		Arr::insert( $tabs, [
			'redirections' => [
				'icon'  => 'dashicons dashicons-controls-forward',
				'title' => esc_html__( 'Redirections', 'cpseo' ),
				/* translators: Link to kb article */
				'desc'  => sprintf( esc_html__( 'Enable Redirections to set up custom 301, 302, 307, 410, or 451 redirections.', 'cpseo' ) ),
				'file'  => $this->directory . '/views/options.php',
			],
		], 8 );

		return $tabs;
	}

	/**
	 * Add stats into admin dashboard.
	 *
	 * @codeCoverageIgnore
	 */
	public function dashboard_widget() {
		$data = DB::get_stats();
		?>
		<br />
		<h3><?php esc_html_e( 'Redirections Stats', 'cpseo' ); ?></h3>
		<ul>
			<li><span><?php esc_html_e( 'Redirections Count', 'cpseo' ); ?></span><?php echo Str::human_number( $data->total ); ?></li>
			<li><span><?php esc_html_e( 'Redirections Hits', 'cpseo' ); ?></span><?php echo Str::human_number( $data->hits ); ?></li>
		</ul>
		<?php
	}

	/**
	 * Initialize.
	 *
	 * @codeCoverageIgnore
	 */
	public function init() {
		if ( ! empty( $_REQUEST['delete_all'] ) ) {
			check_admin_referer( 'bulk-redirections' );
			DB::clear_trashed();
			return;
		}

		$action = Admin::get_request_action();
		if ( false === $action || empty( $_REQUEST['redirection'] ) || 'edit' === $action ) {
			return;
		}

		check_admin_referer( 'bulk-redirections' );

		$ids = (array) wp_parse_id_list( $_REQUEST['redirection'] );
		if ( empty( $ids ) ) {
			Helper::add_notification( 'No valid id found.' );
			return;
		}

		$notice = $this->perform_action( $action, $ids );
		if ( $notice ) {
			Helper::add_notification( $notice, [ 'type' => 'success' ] );
			return;
		}

		Helper::add_notification( esc_html__( 'No valid action found.', 'cpseo' ) );
	}

	/**
	 * Handle AJAX request.
	 *
	 * @codeCoverageIgnore
	 */
	public function handle_ajax() {
		$action = WordPress::get_request_action();
		if ( false === $action ) {
			return;
		}

		check_ajax_referer( 'redirection_list_action', 'security' );
		$this->has_cap_ajax( 'redirections' );

		$id     = Param::request( 'redirection', 0, FILTER_VALIDATE_INT );
		$action = str_replace( 'cpseo_redirection_', '', $action );

		if ( ! $id ) {
			$this->error( esc_html__( 'No valid id found.', 'cpseo' ) );
		}

		$notice = $this->perform_action( $action, $id );
		if ( $notice ) {
			$this->success( $notice );
		}

		$this->error( esc_html__( 'No valid action found.', 'cpseo' ) );
	}

	/**
	 * Perform action on database.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param  string        $action Action to perform.
	 * @param  integer|array $ids    Rows to perform on.
	 * @return string
	 */
	private function perform_action( $action, $ids ) {
		$status  = [
			'activate'   => 'active',
			'deactivate' => 'inactive',
			'trash'      => 'trashed',
			'restore'    => 'active',
		];
		$message = [
			'activate'   => esc_html__( 'Redirection successfully activated.', 'cpseo' ),
			'deactivate' => esc_html__( 'Redirection successfully deactivated.', 'cpseo' ),
			'trash'      => esc_html__( 'Redirection successfully moved to Trash.', 'cpseo' ),
			'restore'    => esc_html__( 'Redirection successfully restored.', 'cpseo' ),
		];

		if ( isset( $status[ $action ] ) ) {
			DB::change_status( $ids, $status[ $action ] );
			return $message[ $action ];
		}

		if ( 'delete' === $action ) {
			$count = DB::delete( $ids );
			if ( $count > 0 ) {
				/* translators: delete counter */
				return sprintf( esc_html__( '%d redirection(s) successfully deleted.', 'cpseo' ), $count );
			}
		}

		return false;
	}
}
