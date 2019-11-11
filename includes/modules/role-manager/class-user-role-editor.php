<?php
/**
 * User Role Editor plugin integration.
 *
 * @since      0.1.8
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\Role_Manager

 */

namespace ClassicPress_SEO\Role_Manager;

use ClassicPress_SEO\Helper;
use ClassicPress_SEO\Traits\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * User_Role_Editor class.
 */
class User_Role_Editor {

	use Hooker;

	/**
	 * Members cap group name.
	 *
	 * @var string
	 */
	const GROUP = 'cpseo';

	/**
	 * Hold caps.
	 *
	 * @var array
	 */
	private $caps = [];

	/**
	 * Class Members constructor.
	 */
	public function __construct() {
		$this->filter( 'ure_capabilities_groups_tree', 'register_group' );
		$this->filter( 'ure_custom_capability_groups', 'register_capability_groups', 10, 2 );

		$this->caps = Helper::get_capabilities();
	}

	/**
	 * Adds ClassicPress SEO capability group in the User Role Editor plugin.
	 *
	 * @param  array $groups Current groups.
	 * @return array Filtered list of capabilty groups.
	 */
	public function register_group( $groups = [] ) {
		$groups = (array) $groups;

		$groups[ self::GROUP ] = [
			'caption' => esc_html__( 'ClassicPress SEO', 'cpseo' ),
			'parent'  => 'custom',
			'level'   => 3,
		];

		return $groups;
	}

	/**
	 * Adds capabilities to the ClassicPress SEO group in the User Role Editor plugin.
	 *
	 * @param  array  $groups Current capability groups.
	 * @param  string $cap_id Capability identifier.
	 * @return array List of filtered groups.
	 */
	public function register_capability_groups( $groups = [], $cap_id = '' ) {
		if ( array_key_exists( $cap_id, $this->caps ) ) {
			$groups   = (array) $groups;
			$groups[] = self::GROUP;
		}

		return $groups;
	}
}
