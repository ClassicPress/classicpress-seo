<?php
/**
 * The Search Console Module
 *
 * @since      0.3.0
 * @package    Classic_SEO
 * @subpackage Classic_SEO\modules
 *
 */

namespace Classic_SEO\Search_Console;

use Exception;
use Classic_SEO\Helper;
use Classic_SEO\Module\Base;
use Classic_SEO\Admin\Admin_Helper;
use Classic_SEO\Traits\Ajax;
use Classic_SEO\Admin\Page;
use Classic_SEO\Helpers\Arr;
use Classic_SEO\Helpers\Str;
use Classic_SEO\Helpers\Param;
use Classic_SEO\Helpers\Conditional;

defined( 'ABSPATH' ) || exit;

/**
 * Search_Console class.
 */
class Search_Console extends Base {

	use Ajax;

	/**
	 * Hold current tab id.
	 *
	 * @var string
	 */
	public $current_tab;

	/**
	 * Hold current filters.
	 *
	 * @var array
	 */
	public $filters = null;

	/**
	 * The Constructor
	 */
	public function __construct() {
		if ( Conditional::is_heartbeat() ) {
			return;
		}

		$directory = dirname( __FILE__ );
		$this->config(
			[
				'id'        => 'search-console',
				'directory' => $directory,
				'help'      => [
					'title' => esc_html__( 'Search Console', 'cpseo' ),
					'view'  => $directory . '/views/help.php',
				],
			]
		);
		parent::__construct();

		Client::get();
		Data_Fetcher::get();

		if ( is_admin() ) {
			if ( Helper::has_cap( 'search_console' ) ) {
				$this->action( 'cpseo/dashboard/widget', 'dashboard_widget', 10 );
				$this->filter( 'cpseo/settings/general', 'add_settings' );
			}

			$this->action( 'cpseo/admin/enqueue_scripts', 'enqueue' );

			// AJAX.
			$this->ajax( 'search_console_authentication', 'authentication' );
			$this->ajax( 'search_console_deauthentication', 'deauthentication' );
			$this->ajax( 'search_console_get_profiles', 'get_profiles' );
			$this->ajax( 'search_console_delete_cache', 'delete_cache' );
			$this->ajax( 'search_console_get_cache', 'start_background_process' );
			$this->set_current_tab();
		}
	}

	/**
	 * Enqueue scripts for the metabox.
	 */
	public function enqueue() {
		if ( ! wp_script_is( 'moment', 'registered' ) ) {
			wp_register_script( 'moment', cpseo()->plugin_url() . 'assets/vendor/moment.js', [], cpseo()->version );
		}
	}

	/**
	 * Set current tab field.
	 */
	private function set_current_tab() {
		if ( ! $this->page->is_current_page() ) {
			return;
		}

		$this->get_filters();
		$this->current_tab = Param::get( 'view', 'overview' );

		if ( ! Client::get()->is_authenticated() ) {
			return;
		}

		$class = 'Classic_SEO\Search_Console\\' . ucfirst( $this->current_tab );
		if ( class_exists( $class ) ) {
			$this->{$this->current_tab} = new $class();
		}
	}

	/**
	 * Render dashboard widget.
	 */
	public function dashboard_widget() {
		if ( ! Client::get()->is_authenticated() ) {
			return;
		}

		$today     = Helper::get_midnight( time() );
		$week      = $today - ( DAY_IN_SECONDS * 7 );
		$data_info = DB::data_info(
			[
				'start_date' => date_i18n( 'Y-m-d', $week ),
				'end_date'   => date_i18n( 'Y-m-d', $today ),
			]
		);
		?>
		<h3><?php esc_html_e( 'Search Console Stats', 'cpseo' ); ?></h3>
		<ul>
			<li><span><?php esc_html_e( 'Total Keywords', 'cpseo' ); ?></span><?php echo Str::human_number( $data_info['keywords'] ); ?></li>
			<li><span><?php esc_html_e( 'Total Pages', 'cpseo' ); ?></span><?php echo Str::human_number( $data_info['pages'] ); ?></li>
			<li><span><?php esc_html_e( 'Total Clicks', 'cpseo' ); ?></span><?php echo Str::human_number( $data_info['totals']->clicks ); ?></li>
			<li><span><?php esc_html_e( 'Total Impressions', 'cpseo' ); ?></span><?php echo Str::human_number( $data_info['totals']->impressions ); ?></li>
			<li><span><?php esc_html_e( 'Average Position', 'cpseo' ); ?></span><?php echo round( $data_info['totals']->position, 2 ); ?></li>
			<li><span><?php esc_html_e( 'Average CTR', 'cpseo' ); ?></span><?php echo round( $data_info['totals']->ctr, 2 ); ?></li>
		</ul>
		<?php
	}

	/**
	 * Register admin page.
	 */
	public function register_admin_page() {
		$plugin_uri = untrailingslashit( plugin_dir_url( __FILE__ ) );
		$this->page = new Page(
			'cpseo-search-console',
			esc_html__( 'Search Console', 'cpseo' ),
			[
				'position'   => 12,
				'parent'     => 'cpseo',
				'capability' => 'cpseo_search_console',
				'render'     => $this->directory . '/views/main.php',
				'classes'    => [ 'cpseo-page' ],
				'help'       => [
					'search-console-overview'  => [
						'title'   => esc_html__( 'Overview', 'cpseo' ),
						'content' => '<p>' . esc_html__( 'Connect Classic SEO with Google Search Console to see the most important information from Google directly in your ClassicPress dashboard.', 'cpseo' ) . '</p>',
					],
					'search-console-analytics' => [
						'title'   => esc_html__( 'Screen Content', 'cpseo' ),
						'content' => '<p>' . esc_html__( 'The Search Analytics tab will give you insights about how your site performs in search engines: you can see the top search queries to find your site and your most popular landing pages.', 'cpseo' ) . '</p>',
					],
					'search-console-sitemaps'  => [
						'title'   => esc_html__( 'Available Actions', 'cpseo' ),
						'content' => '<p>' . esc_html__( 'The Sitemaps tab gives you an overview of the sitemaps submitted to the Search Console.', 'cpseo' ) . '</p>',
					],
				],
				'assets'     => [
					'styles'  => [
						'font-awesome'             => cpseo()->plugin_url() . 'assets/vendor/font-awesome/css/font-awesome.min.css',
						'jquery-date-range-picker' => cpseo()->plugin_url() . 'assets/vendor/date-range-picker/daterangepicker.min.css',
						'cpseo-search-console' => $plugin_uri . '/assets/search-console.css',
					],
					'scripts' => [
						'cpseo-common' => '',
						'moment'           => cpseo()->plugin_url() . 'assets/vendor/moment.js',
						'date-picker'      => cpseo()->plugin_url() . 'assets/vendor/date-range-picker/jquery.daterangepicker.min.js',
						'google-charts'    => '//www.gstatic.com/charts/loader.js',
						'cpseo-sc'     => $plugin_uri . '/assets/search-console.js',
					],
				],
			]
		);
	}

	/**
	 * Add module settings into general optional panel.
	 *
	 * @param array $tabs Array of option panel tabs.
	 *
	 * @return array
	 */
	public function add_settings( $tabs ) {
		Arr::insert(
			$tabs,
			[
				'search-console' => [
					'icon'  => 'fa fa-search-plus',
					'title' => esc_html__( 'Search Console', 'cpseo' ),
					'desc'  => esc_html__( 'Connect to Google Search Console profile to see the most important information from Google. %s.', 'cpseo' ),
					'file'  => $this->directory . '/views/options.php',
				],
			],
			9
		);

		return $tabs;
	}

	/**
	 * Display tabs.
	 */
	public function display_nav() {
		$tabs = [
			'overview'  => esc_html__( 'Overview', 'cpseo' ),
			'analytics' => esc_html__( 'Search Analytics', 'cpseo' ),
			'sitemaps'  => esc_html__( 'Sitemaps', 'cpseo' ),
		];

		$this->is_sitemap_available( $tabs );
		$filters = $this->get_filters();
		?>
		<div class="cpseo-date-selector-container">
			<strong><?php echo esc_html( get_admin_page_title() ); ?></strong>
			<?php foreach ( $tabs as $id => $label ) : ?>
			<a class="<?php echo $id === $this->current_tab ? 'active' : ''; ?>" href="<?php echo esc_url( Helper::get_admin_url( 'search-console', 'view=' . $id ) ); ?>" title="<?php echo $label; ?>"><?php echo $label; ?></a>
			<?php endforeach; ?>

			<?php if ( in_array( $this->current_tab, [ 'overview', 'analytics' ], true ) ) : ?>
			<form method="post" action="" class="date-selector">
				<?php if ( 'analytics' === $this->current_tab ) : ?>
				<input type="text" id="cpseo-search" name="s" class="regular-text" placeholder="Search&hellip;" value="<?php echo isset( $_POST['s'] ) ? esc_attr( $_POST['s'] ) : ''; ?>">
				<select id="cpseo-overview-type" name="dimension">
					<option value="query"<?php selected( 'query', $filters['dimension'] ); ?>>Keywords</option>
					<option value="page"<?php selected( 'page', $filters['dimension'] ); ?>>Pages</option>
				</select>
				<?php endif; ?>
				<span class="input-group">
					<span class="dashicons dashicons-calendar-alt"></span>
					<input type="text" id="cpseo-date-selector" value="<?php echo $filters['picker']; ?>">
				</span>
				<input type="hidden" id="cpseo-start-date" name="start_date" value="<?php echo $filters['start']; ?>">
				<input type="hidden" id="cpseo-end-date" name="end_date" value="<?php echo $filters['end']; ?>">
			</form>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Authenticate google oauth code.
	 */
	public function authentication() {
		check_ajax_referer( 'cpseo-ajax-nonce', 'security' );
		$this->has_cap_ajax( 'search_console' );

		$code = Param::post( 'code' );
		$code = $code ? trim( wp_unslash( $code ) ) : false;
		if ( ! $code ) {
			$this->error( esc_html__( 'No authentication code found.', 'cpseo' ) );
		}

		$this->success( Client::get()->get_access_token( $code ) );
	}

	/**
	 * Disconnect google authentication.
	 */
	public function deauthentication() {
		check_ajax_referer( 'cpseo-ajax-nonce', 'security' );
		$this->has_cap_ajax( 'search_console' );
		Client::get()->disconnect();
		Data_Fetcher::get()->kill_process();
		$this->success( 'done' );
	}

	/**
	 * Get profiles list.
	 */
	public function get_profiles() {
		check_ajax_referer( 'cpseo-ajax-nonce', 'security' );
		$this->has_cap_ajax( 'search_console' );

		$profiles = Client::get()->get_profiles();
		if ( empty( $profiles ) ) {
			$this->error( 'No profiles found.' );
		}

		foreach ( $profiles as $key => $value ) {
			$profiles[ $key ] = str_replace( 'sc-domain:', __( 'Domain Property: ', 'cpseo' ), $value );
		}

		$this->success(
			[
				'profiles' => $profiles,
				'selected' => $this->select_profile( $profiles ),
			]
		);
	}

	/**
	 * Select profile
	 *
	 * @param array $profiles Array of fetched profiles.
	 *
	 * @return string
	 */
	private function select_profile( $profiles ) {
		$home_url = home_url( '/', 'https' );
		if ( in_array( $home_url, $profiles, true ) ) {
			return $home_url;
		}

		$home_url = home_url( '/', 'http' );
		if ( in_array( $home_url, $profiles, true ) ) {
			return $home_url;
		}

		return '';
	}

	/**
	 * Delete cache.
	 */
	public function delete_cache() {
		check_ajax_referer( 'cpseo-ajax-nonce', 'security' );
		$this->has_cap_ajax( 'search_console' );

		$days = Param::get( 'days', false, FILTER_VALIDATE_INT );
		if ( ! $days ) {
			$this->error( esc_html__( 'Not a valid settings founds to delete cache.', 'cpseo' ) );
		}

		DB::delete( $days );
		Data_Fetcher::get()->kill_process();
		$db_info            = DB::info();
		$db_info['message'] = sprintf( '<div class="cpseo-console-db-info"><span class="dashicons dashicons-calendar-alt"></span> Cached Days: <strong>%s</strong></div>', $db_info['days'] ) .
		sprintf( '<div class="cpseo-console-db-info"><span class="dashicons dashicons-editor-ul"></span> Data Rows: <strong>%s</strong></div>', Str::human_number( $db_info['rows'] ) ) .
		sprintf( '<div class="cpseo-console-db-info"><span class="dashicons dashicons-editor-code"></span> Size: <strong>%s</strong></div>', size_format( $db_info['size'] ) );

		$this->success( $db_info );
	}

	/**
	 * Get cache progressively.
	 */
	public function start_background_process() {
		check_ajax_referer( 'cpseo-ajax-nonce', 'security' );
		$this->has_cap_ajax( 'search_console' );

		if ( ! Client::get()->is_authenticated() ) {
			$this->error( esc_html__( 'Google oAuth is not authorized.', 'cpseo' ) );
		}

		try {
			Data_Fetcher::get()->start_process( Param::get( 'days', 90, FILTER_VALIDATE_INT ) );
			$this->success( 'Data fetching started in the background.' );
		} catch ( Exception $error ) {
			$this->error( $error->getMessage() );
		}
	}

	/**
	 * Get current filters.
	 *
	 * @return array
	 */
	public function get_filters() {
		if ( ! is_null( $this->filters ) ) {
			return $this->filters;
		}

		$today     = Helper::get_midnight( time() );
		$end       = $this->get_filter_data( 'end_date', ( $today - ( DAY_IN_SECONDS * 1 ) ) );
		$start     = $this->get_filter_data( 'start_date', ( $today - ( DAY_IN_SECONDS * 30 ) ) );
		$dimension = $this->get_filter_data( 'dimension', 'query' );

		$start_date = date_i18n( 'Y-m-d', $start );
		$end_date   = date_i18n( 'Y-m-d', $end );
		$picker     = $start_date . ' to ' . $end_date;

		// Previous Dates.
		$prev_end_date   = $start - ( DAY_IN_SECONDS * 1 );
		$prev_start_date = $prev_end_date - abs( $start - $end );
		$prev_end_date   = date_i18n( 'Y-m-d', $prev_end_date );
		$prev_start_date = date_i18n( 'Y-m-d', $prev_start_date );

		// Difference.
		$diff          = abs( $start - $end ) / DAY_IN_SECONDS;
		$this->filters = compact( 'dimension', 'diff', 'picker', 'today', 'start', 'end', 'start_date', 'end_date', 'prev_start_date', 'prev_end_date' );

		return $this->filters;
	}

	/**
	 * If sitemap not available remove tab.
	 *
	 * @param array $tabs Array of tabs.
	 */
	private function is_sitemap_available( &$tabs ) {
		if ( ! Client::get()->is_authenticated() ) {
			return;
		}

		$this->sitemaps = new Sitemaps;
		if ( $this->sitemaps->selected_site_is_domain_property() ) {
			unset( $tabs['sitemaps'] );
		}
	}

	/**
	 * Get filter data.
	 *
	 * @param string $filter  Filter key.
	 * @param string $default Filter default value.
	 *
	 * @return mixed
	 */
	private function get_filter_data( $filter, $default ) {
		$cookie_key = 'cpseo_sc_' . $filter;
		if ( isset( $_POST[ $filter ] ) && ! empty( $_POST[ $filter ] ) ) {
			$value = Param::post( $filter );
			setcookie( $cookie_key, $value, time() + ( HOUR_IN_SECONDS * 30 ), COOKIEPATH, COOKIE_DOMAIN );
			return $value;
		}

		if ( ! empty( $_COOKIE[ $cookie_key ] ) ) {
			$value = $_COOKIE[ $cookie_key ];
			return $value;
		}

		return $default;
	}
}
