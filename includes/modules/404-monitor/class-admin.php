<?php
/**
 * The 404 Monitor Module
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Monitor

 */

namespace Classic_SEO\Monitor;

use Classic_SEO\Helper;
use Classic_SEO\Module\Base;
use Classic_SEO\Admin\Page;
use Classic_SEO\Helpers\Str;
use Classic_SEO\Helpers\Arr;
use Classic_SEO\Helpers\Param;
use Classic_SEO\Helpers\WordPress;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 */
#[\AllowDynamicProperties]
class Admin extends Base {

	/**
	 * The Constructor.
	 *
	 * @codeCoverageIgnore
	 */
	public function __construct() {
		$directory = dirname( __FILE__ );
		$this->config(
			[
				'id'             => '404-monitor',
				'directory'      => $directory,
				'table'          => 'Classic_SEO\Monitor\Table',
				'help'           => [
					'title' => esc_html__( '404 Monitor', 'cpseo' ),
					'view'  => $directory . '/views/help.php',
				],
				'screen_options' => [
					'id'      => 'cpseo_404_monitor_per_page',
					'default' => 100,
				],
			]
		);
		parent::__construct();

		if ( $this->page->is_current_page() ) {
			$this->action( 'init', 'init' );
		}

		if ( Helper::has_cap( '404_monitor' ) ) {
			$this->action( 'cpseo/dashboard/widget', 'dashboard_widget', 11 );
			$this->filter( 'cpseo/settings/general', 'add_settings' );
		}
	}

	/**
	 * Initialize.
	 *
	 * @codeCoverageIgnore
	 */
	public function init() {
		$action = WordPress::get_request_action();
		if ( false === $action || ! in_array( $action, [ 'delete', 'clear_log' ], true ) ) {
			return;
		}

		if ( ! check_admin_referer( 'bulk-events' ) ) {
			check_admin_referer( '404_delete_log', 'security' );
		}

		$action = 'do_' . $action;
		$this->$action();
	}

	/**
	 * Delete selected log.
	 */
	protected function do_delete() {
		$log = Param::request( 'log', '', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( empty( $log ) ) {
			return;
		}

		$count = DB::delete_log( $log );
		if ( $count > 0 ) {
			Helper::add_notification(
				/* translators: delete counter */
				sprintf( esc_html__( '%d log(s) deleted.', 'cpseo' ), $count ),
				[ 'type' => 'success' ]
			);
		}
	}

	/**
	 * Clear logs.
	 */
	protected function do_clear_log() {
		$count = DB::get_count();
		DB::clear_logs();

		Helper::add_notification(
			/* translators: delete counter */
			sprintf( esc_html__( 'Log cleared - %d items deleted.', 'cpseo' ), $count ),
			[ 'type' => 'success' ]
		);
	}

	/**
	 * Register admin page.
	 *
	 * @codeCoverageIgnore
	 */
	public function register_admin_page() {

		$dir = $this->directory . '/views/';
		$uri = untrailingslashit( plugin_dir_url( __FILE__ ) );

		$this->page = new Page( 'cpseo-404-monitor', esc_html__( '404 Monitor', 'cpseo' ), [
			'position'   => 12,
			'parent'     => 'cpseo',
			'capability' => 'cpseo_404_monitor',
			'render'     => $dir . 'main.php',
			'classes'    => [ 'cpseo-page' ],
			'assets'     => [
				'styles'  => [ 'cpseo-common' => '' ],
				'scripts' => [ 'cpseo-404-monitor' => $uri . '/assets/404-monitor.js' ],
			],
		]);

		if ( $this->page->is_current_page() ) {
			Helper::add_json( 'logConfirmClear', esc_html__( 'Are you sure you wish to delete all 404 error logs?', 'cpseo' ) );
			Helper::add_json( 'redirectionsUri', Helper::get_admin_url( 'redirections' ) );
		}
	}

	/**
	 * Add module settings into general optional panel.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param array $tabs Array of option panel tabs.
	 *
	 * @return array
	 */
	public function add_settings( $tabs ) {

		Arr::insert( $tabs, [
			'404-monitor' => [
				'icon'  => 'dashicons dashicons-no',
				'title' => esc_html__( '404 Monitor', 'cpseo' ),
				/* translators: 1. Link to kb article 2. Link to redirection setting scree */
				'desc'  => sprintf( esc_html__( 'The 404 monitor lets you see broken links (404 not found errors) on your site. Turn on %s too to redirect broken URLs.', 'cpseo' ), '<a href="' . Helper::get_admin_url( 'options-general#setting-panel-redirections' ) . '" target="_blank">' . esc_html__( 'Redirections', 'cpseo' ) . '</a>' ),
				'file'  => $this->directory . '/views/options.php',
			],
		], 7 );

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
		<h3><?php esc_html_e( '404 Monitor Stats', 'cpseo' ); ?></h3>
		<ul>
			<li><span><?php esc_html_e( '404 Monitor Log Count', 'cpseo' ); ?></span><?php echo Str::human_number( $data->total ); ?></li>
			<li><span><?php esc_html_e( '404 URI Hits', 'cpseo' ); ?></span><?php echo Str::human_number( $data->hits ); ?></li>
		</ul>
		<?php
	}
}
