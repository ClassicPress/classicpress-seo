<?php
/**
 * Menbers plugin integration.
 *
 * @since      0.1.8
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\Role_Manager

 */

namespace ClassicPress_SEO\Role_Manager;

use ClassicPress_SEO\Helper;
use ClassicPress_SEO\Traits\Hooker;
use ClassicPress_SEO\Helpers\Param;

defined( 'ABSPATH' ) || exit;

/**
 * Members class.
 */
class Members {

	use Hooker;

	/**
	 * Members cap group name.
	 *
	 * @var string
	 */
	const GROUP = 'cpseo';

	/**
	 * Class Members constructor.
	 */
	public function __construct() {
		$this->action( 'members_register_caps', 'register_caps' );
		$this->action( 'members_register_cap_groups', 'register_cap_groups' );
	}

	/**
	 * Registers cap group.
	 */
	public function register_cap_groups() {
		members_register_cap_group( self::GROUP, [
			'label'    => esc_html__( 'ClassicPress SEO', 'cpseo' ),
			'caps'     => [],
			'icon'     => 'dashicons-chart-area',
			'priority' => 30,
		]);
	}

	/**
	 * Registers caps.
	 */
	public function register_caps() {
		$caps = Helper::get_capabilities();
		if ( 'administrator' === Param::get( 'role' ) ) {
			$caps['cpseo_edit_htaccess'] = esc_html__( 'Edit .htaccess', 'cpseo' );
		}

		foreach ( $caps as $key => $value ) {
			members_register_cap( $key, [
				'label' => html_entity_decode( $value ),
				'group' => self::GROUP,
			]);
		}
	}
}
