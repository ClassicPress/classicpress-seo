<?php
/**
 * The admin engine of the plugin.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin
 */


namespace Classic_SEO\Admin;

use Classic_SEO\Helper;
use Classic_SEO\Updates;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Admin\Param;
use Classic_SEO\Helpers\Conditional;
use Classic_SEO\Search_Console\Search_Console;

defined( 'ABSPATH' ) || exit;

/**
 * Engine class.
 *
 * @codeCoverageIgnore
 */
class Engine {

	use Hooker;

	/**
	 * The Constructor.
	 */
	public function __construct() {

		cpseo()->admin        = new Admin;
		cpseo()->admin_assets = new Assets;

		$this->search_console_ajax();

		$runners = [
			cpseo()->admin,
			cpseo()->admin_assets,		
			new Admin_Menu,
			new Option_Center,
			new Notices,
			new CMB2_Fields,
			new Metabox,
			new Post_Columns,
			new Post_Filters,
			new Import_Export,
			new Updates,
			new Watcher,
		];

		foreach ( $runners as $runner ) {
			$runner->hooks();
		}

		/**
		 * Fires when admin is loaded.
		 */
		$this->do_action( 'admin/loaded' );
	}


	/**
	 * Search console ajax handler.
	 */
	private function search_console_ajax() {
		if ( ! Conditional::is_ajax() || class_exists( 'Search_Console' ) ) {
			return;
		}

		$action = Param::post( 'action' );
		if ( $action && in_array( $action, [ 'cpseo_search_console_authentication', 'cpseo_search_console_deauthentication', 'cpseo_search_console_get_profiles' ], true ) ) {
			Helper::update_modules( [ 'search-console' => 'on' ] );
			new Search_Console;
		}
	}
}
