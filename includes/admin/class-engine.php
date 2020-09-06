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

}
