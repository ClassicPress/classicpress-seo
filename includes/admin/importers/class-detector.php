<?php
/**
 * The functionality to detect whether we should import from another SEO plugin.
 *
 * @since      0.2.0
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin\Importers
 */


namespace Classic_SEO\Admin\Importers;

use Classic_SEO\Traits\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Detector class.
 */
class Detector {

	use Hooker;

	/**
	 * Plugins we can import from.
	 *
	 * @var array
	 */
	public static $plugins = null;

	/**
	 * Detects whether we can import anything or not.
	 *
	 * @return array List of plugins we can import from.
	 */
	public function detect() {

		$this->requirements();
		if ( ! is_null( self::$plugins ) ) {
			return self::$plugins;
		}
		self::$plugins = [];

		$plugins = $this->get();

		foreach ( $plugins as $slug => $plugin ) {
			if ( ! $this->is_detectable( $plugin, $plugins ) ) {
				continue;
			}

			$this->can_import( $slug, $plugin );
		}

		return self::$plugins;
	}

	/**
	 * Run import class.
	 *
	 * @param Plugin_Importer $importer The importer that needs to perform this action.
	 * @param string          $action   The action to perform.
	 * @param string          $perform  The action to perform when running import action.
	 */
	public function run( $importer, $action = 'detect', $perform = '' ) {
		if ( 'cleanup' === $action ) {
			return $importer->run_cleanup();
		} elseif ( 'import' === $action ) {
			return $importer->run_import( $perform );
		}

		return $importer->run_detect();
	}

	/**
	 * Run action by slug.
	 *
	 * @param string $slug    The importer slug that needs to perform this action.
	 * @param string $action  The action to perform.
	 * @param string $perform The action to perform when running import action.
	 */
	public static function run_by_slug( $slug, $action, $perform = '' ) {
		$detector  = new self;
		$importers = $detector->get();
		if ( ! isset( $importers[ $slug ] ) ) {
			return false;
		}

		$importer = $importers[ $slug ];
		$importer = new $importer['class']( $importer['file'] );
		$status   = $detector->run( $importer, $action, $perform );

		return \compact( 'importer', 'status' );
	}

	/**
	 * Deactivate all plugins.
	 */
	public static function deactivate_all() {
		$detector = new Detector;
		$plugins  = $detector->get();
		foreach ( $plugins as $plugin ) {
			deactivate_plugins( $plugin['file'] );
		}
	}

	/**
	 * Get the list of available importers.
	 *
	 * @return array Available importers.
	 */
	public function get() {
		return $this->do_filter( 'importers/detect_plugins', [
			'rankmath'         => [
				'class' => '\\Classic_SEO\\Admin\\Importers\\RankMath',
				'file'  => 'seo-by-rank-math/rank-math.php',
			],
			'yoast'            => [
				'class'   => '\\Classic_SEO\\Admin\\Importers\\Yoast',
				'file'    => 'wordpress-seo/wp-seo.php',
				'premium' => 'yoast-premium',
			],
			'aioseo'           => [
				'class' => '\\Classic_SEO\\Admin\\Importers\\AIOSEO',
				'file'  => 'all-in-one-seo-pack/all_in_one_seo_pack.php',
			],
			'seo-framework'    => [
				'class' => '\\Classic_SEO\\Admin\\Importers\\SEO_Framework',
				'file'  => 'all-in-one-seo-pack/class-seo-framework.php',
			],
		]);
	}

	/**
	 * Check requirements.
	 */
	private function requirements() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
	}

	/**
	 * Can import plugin data.
	 *
	 * @param string $slug   Plugin slug.
	 * @param array  $plugin Plugin data.
	 */
	private function can_import( $slug, $plugin ) {
		$importer = new $plugin['class']( $plugin['file'] );

		if ( $importer->run_detect() ) {
			self::$plugins[ $slug ] = [
				'name'    => $importer->get_plugin_name(),
				'file'    => $importer->get_plugin_file(),
				'choices' => $importer->get_choices(),
			];
		}
	}

	/**
	 * Check if plugin is detectable.
	 *
	 * @param array $check   Plugin to check.
	 * @param array $plugins Plugins data.
	 *
	 * @return bool
	 */
	private function is_detectable( $check, $plugins ) {
		// Check if parent is set.
		if ( isset( $check['parent'] ) && isset( self::$plugins[ $check['parent'] ] ) ) {
			return false;
		}
		
		return true;
	}
}
