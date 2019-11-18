<?php
/**
 * The ACF Module
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\modules
 */


namespace Classic_SEO\ACF;

use Classic_SEO\Helper;
use Classic_SEO\Admin\Admin_Helper;
use Classic_SEO\Traits\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * ACF class.
 */
class ACF {
	use Hooker;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		if ( ! Admin_Helper::is_post_edit() && ! Admin_Helper::is_term_edit() ) {
			return;
		}

		$this->action( 'cpseo/admin/enqueue_scripts', 'enqueue' );
	}

	/**
	 * Enqueue styles and scripts for the metabox.
	 */
	public function enqueue() {
		if ( Admin_Helper::is_post_edit() ) {
			wp_enqueue_script( 'cpseo-acf-post-analysis', cpseo()->plugin_url() . 'assets/admin/js/acf-analysis.js', [ 'cpseo-post-metabox' ], cpseo()->version, true );
		}

		if ( Admin_Helper::is_term_edit() ) {
			wp_enqueue_script( 'cpseo-acf-term-analysis', cpseo()->plugin_url() . 'assets/admin/js/acf-analysis.js', [ 'cpseo-term-metabox' ], cpseo()->version, true );
		}

		Helper::add_json( 'acf', $this->get_config() );
	}

	/**
	 * Get Config data
	 *
	 * @return array The config data.
	 */
	private function get_config() {
		$config = [
			'pluginName'      => 'cpseo-acf',
			'refreshRate'     => 1000,
			'headlines'       => [],
			'enableReload'    => true,
			'blacklistFields' => $this->get_blacklist_fields(),
		];

		return $this->do_filter( 'acf/config', $config );
	}

	/**
	 * Get blacklisted fields.
	 *
	 * @return array The Blacklisted fields.
	 */
	private function get_blacklist_fields() {
		$blacklist_type = [
			'number',
			'password',
			'file',
			'select',
			'checkbox',
			'radio',
			'true_false',
			'post_object',
			'page_link',
			'relationship',
			'user',
			'date_picker',
			'color_picker',
			'message',
			'tab',
			'repeater',
			'flexible_content',
			'group',
		];

		return [
			'type' => $blacklist_type,
			'name' => [],
		];
	}
}
