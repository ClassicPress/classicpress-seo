<?php
/**
 * Settings and stuff
 *
 * @since      0.1.8
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\Admin
 */

namespace ClassicPress_SEO\Admin;

use ClassicPress_SEO\CMB2;
use ClassicPress_SEO\Helper;
use ClassicPress_SEO\Traits\Hooker;
use ClassicPress_SEO\Admin\Admin_Helper;


defined( 'ABSPATH' ) || exit;

/**
 * Registration class.
 */
class Registration {

	use Hooker;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->action( 'admin_init', 'render_page', 30 );
	}


	/**
	 * Output the admin page.
	 */
	public function render_page() {
		$assets = new Assets;
		$assets->register();

		wp_styles()->done  = [];
		wp_scripts()->done = [];

		// Enqueue styles.
		\CMB2_hookup::enqueue_cmb_css();
		\CMB2_hookup::enqueue_cmb_js();
	}

}
