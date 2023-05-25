<?php
/**
 * The Module
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Module
 */

namespace Classic_SEO\Module;

use Classic_SEO\Helper;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Helpers\Conditional;

defined( 'ABSPATH' ) || exit;

/**
 * Manager class.
 */
class Manager {

	use Hooker, Conditional;

	/**
	 * Holds modules.
	 *
	 * @var array
	 */
	public $modules = [];

	/**
	 * Holds module objects.
	 *
	 * @var array
	 */
	private $controls = [];

	/**
	 * The Constructor.
	 */
	public function __construct() {
		if ( Manager::is_heartbeat() ) {
			return;
		}

		$this->action( 'plugins_loaded', 'setup_modules' );
		$this->filter( 'cpseo/modules', 'setup_core', 1 );
		$this->filter( 'cpseo/modules', 'setup_admin_only', 1 );
		$this->filter( 'cpseo/modules', 'setup_internals', 1 );
		$this->filter( 'cpseo/modules', 'setup_3rd_party', 1 );

		$this->action( 'plugins_loaded', 'load_modules', 11 );
		add_action( 'cpseo/module_changed', [ '\Classic_SEO\Admin\Watcher', 'module_changed' ], 10, 2 );
	}

	/**
	 * Include default modules support.
	 */
	public function setup_modules() {
		/**
		 * Filters the array of modules available to be activated.
		 *
		 * @param array $modules Array of available modules.
		 */
		$modules = $this->do_filter( 'modules', [] );

		ksort( $modules );
		foreach ( $modules as $id => $module ) {
			$this->add_module( $id, $module );
		}
	}

	/**
	 * Setup core modules.
	 *
	 * @param array $modules Array of modules.
	 *
	 * @return array
	 */
	public function setup_core( $modules ) {
		$modules['404-monitor'] = [
			'title'    => esc_html__( '404 Monitor', 'cpseo' ),
			'desc'     => esc_html__( 'Records the URLs that return 404 Errors.', 'cpseo' ),
			'class'    => 'Classic_SEO\Monitor\Monitor',
			'icon'     => 'dashicons-editor-unlink',
			'settings' => Helper::get_admin_url( 'options-general' ) . '#setting-panel-404-monitor',
		];

		$modules['local-seo'] = [
			'title'    => esc_html__( 'Local SEO & Google Knowledge Graph', 'cpseo' ),
			'desc'     => esc_html__( 'Optimize your website and posts for local searches.', 'cpseo' ),
			'class'    => 'Classic_SEO\Local_Seo\Local_Seo',
			'icon'     => 'dashicons-location-alt',
			'settings' => Helper::get_admin_url( 'options-titles' ) . '#setting-panel-local',
		];

		$modules['redirections'] = [
			'title'    => esc_html__( 'Redirections', 'cpseo' ),
			'desc'     => esc_html__( 'Redirect non-existent content with 301 and 302 status codes.', 'cpseo' ),
			'class'    => 'Classic_SEO\Redirections\Redirections',
			'icon'     => 'dashicons-randomize',
			'settings' => Helper::get_admin_url( 'options-general' ) . '#setting-panel-redirections',
		];

		$modules['rich-snippet'] = [
			'title'    => esc_html__( 'Rich Snippets', 'cpseo' ),
			'desc'     => esc_html__( 'Enable support for the Rich Snippets.', 'cpseo' ),
			'class'    => 'Classic_SEO\RichSnippet\RichSnippet',
			'icon'     => 'dashicons-awards',
			'settings' => Helper::get_admin_url( 'options-titles' ) . '#setting-panel-post-type-post',
		];

		$modules['sitemap'] = [
			'title'    => esc_html__( 'Sitemap', 'cpseo' ),
			'desc'     => esc_html__( 'Enable sitemap feature.', 'cpseo' ),
			'class'    => 'Classic_SEO\Sitemap\Sitemap',
			'icon'     => 'dashicons-networking',
			'settings' => Helper::get_admin_url( 'options-sitemap' ),
		];

		$modules['link-counter'] = [
			'title' => esc_html__( 'Link Counter', 'cpseo' ),
			'desc'  => esc_html__( 'The total number of links inside your posts.', 'cpseo' ),
			'class' => 'Classic_SEO\Links\Links',
			'icon'  => 'dashicons-admin-links',
		];

		return $modules;
	}

	/**
	 * Setup admin only modules.
	 *
	 * @param array $modules Array of modules.
	 *
	 * @return array
	 */
	public function setup_admin_only( $modules ) {

		$modules['role-manager'] = [
			'title'    => esc_html__( 'Role Manager', 'cpseo' ),
			'desc'     => esc_html__( 'Control who can change Classic SEO settings', 'cpseo' ),
			'class'    => 'Classic_SEO\Role_Manager\Role_Manager',
			'icon'     => 'dashicons-groups',
			'only'     => 'admin',
			'settings' => Helper::get_admin_url( 'role-manager' ),
		];

		return $modules;
	}

	/**
	 * Setup internal modules.
	 *
	 * @param array $modules Array of modules.
	 *
	 * @return array
	 */
	public function setup_internals( $modules ) {

		$modules['robots-txt'] = [
			'title' => esc_html__( 'Robots Txt', 'cpseo' ),
			'only'  => 'internal',
			'class' => 'Classic_SEO\Robots_Txt',
		];

		$modules['status'] = [
			'title' => esc_html__( 'Status', 'cpseo' ),
			'only'  => 'internal',
			'class' => 'Classic_SEO\Status\Status',
		];

		return $modules;
	}

	/**
	 * Setup 3rd party modules.
	 *
	 * @param array $modules Array of modules.
	 *
	 * @return array
	 */
	public function setup_3rd_party( $modules ) {
		if ( class_exists( 'WooCommerce' ) ) {
			$ecom = Conditional::is_woocommerce_active() ? 'WooCommerce' : 'Classic Commerce';
			$modules['woocommerce'] = [
				'title'         => esc_html__( $ecom, 'cpseo' ),
				'desc'          => esc_html__( 'Optimize ' . $ecom . ' Product Pages.', 'cpseo' ),
				'class'         => 'Classic_SEO\WooCommerce\WooCommerce',
				'icon'          => 'dashicons-cart',
				'disabled'      => ( ! Conditional::is_woocommerce_active() && ! Conditional::is_classic_commerce_active() ),
				'disabled_text' => esc_html__( 'Please activate ' . $ecom . ' plugin to use this module.', 'cpseo' ),
			];
		}

		if ( class_exists( 'ACF' ) ) {
			$modules['acf'] = [
				'title'         => esc_html__( 'ACF', 'cpseo' ),
				'desc'          => esc_html__( 'Read and analyze content written in the Advanced Custom Fields..', 'cpseo' ),
				'class'         => 'Classic_SEO\ACF\ACF',
				'icon'          => 'dashicons-editor-table',
				'disabled'      => ( ! function_exists( 'acf' ) ),
				'disabled_text' => esc_html__( 'Please activate ACF plugin to use this module.', 'cpseo' ),
			];
		}

		return $modules;
	}

	/**
	 * Add module.
	 *
	 * @param string $id   Module unique id.
	 * @param array  $args Module configuration.
	 */
	public function add_module( $id, $args = [] ) {
		$this->modules[ $id ] = new Module( $id, $args );
	}

	/**
	 * Display module form to enable/disable them.
	 *
	 * @codeCoverageIgnore
	 */
	public function display_form() {
		if ( ! current_user_can( 'manage_options' ) ) {
			echo 'You don\'t have access to this page.';
			return;
		}
		?>
		<div class="cpseo-ui module-listing">

			<div class="two-col">
			<?php
			foreach ( $this->modules as $module ) :
				if ( ! $module->can_display() ) {
					continue;
				}

				$is_active   = $module->is_active();
				$is_disabled = $module->is_disabled();
				?>
				<div class="col">

					<div class="cpseo-box <?php echo $is_active ? 'active' : ''; ?>">

						<p style="float:right"><?php $module->the_link(); ?></p>

						<span class="dashicons <?php echo $module->get_icon(); ?>"></span>

						<header>
							<h3><?php echo $module->get( 'title' ); ?></h3>
							<p><em><?php echo $module->get( 'desc' ); ?></em></p>
						</header>

						<div class="status wp-clearfix">

							<span class="cpseo-switch">
								<input type="checkbox" class="cpseo-modules" id="module-<?php echo $module->get_id(); ?>" name="modules[]" value="<?php echo $module->get_id(); ?>"<?php checked( $is_active ); ?> <?php disabled( $is_disabled, true ); ?>>
								<label for="module-<?php echo $module->get_id(); ?>" class="<?php echo $is_disabled ? 'cpseo-tooltip' : ''; ?>"><?php esc_html_e( 'Toggle', 'cpseo' ); ?>
									<?php echo $module->has( 'disabled_text' ) ? '<span>' . $module->get( 'disabled_text' ) . '</span>' : ''; ?>
								</label>
								<span class="input-loading"></span>
							</span>

							<label>
								<?php esc_html_e( 'Status:', 'cpseo' ); ?>
								<span class="module-status active-text"><?php echo esc_html__( 'Active', 'cpseo' ); ?> </span>
								<span class="module-status inactive-text"><?php echo esc_html__( 'Inactive', 'cpseo' ); ?> </span>
							</label>

						</div>

					</div>

				</div>
			<?php endforeach; ?>
			</div>

		</div>
		<?php
	}

	/**
	 * Load active modules.
	 */
	public function load_modules() {
		foreach ( $this->modules as $id => $module ) {
			if ( false === $module->can_load_module() ) {
				continue;
			}

			$this->load_module( $id, $module );
		}
	}

	/**
	 * Load single module.
	 *
	 * @param string $id ID of module.
	 * @param Module $module Module instance.
	 */
	private function load_module( $id, $module ) {
		$object_class = $module->get( 'class' );
		if ( $module->is_admin() ) {
			$this->load_module_common( $module );
			if ( ! is_admin() ) {
				return;
			}
		}

		if ( class_exists( $object_class ) ) {
			$this->controls[ $id ] = new $object_class;
		}
	}

	/**
	 * Load module common file.
	 *
	 * @param Module $module Module instance.
	 */
	public function load_module_common( $module ) {
		$object_class = $module->get( 'class' );
		if ( class_exists( $object_class . '_Common' ) ) {
			$module_common_class                             = $object_class . '_Common';
			$this->controls[ $module->get_id() . '_common' ] = new $module_common_class;
		}
	}

	/**
	 * Get module by ID.
	 *
	 * @param string $id ID to get module.
	 *
	 * @return object Module class object.
	 */
	public function get_module( $id ) {
		return isset( $this->controls[ $id ] ) ? $this->controls[ $id ] : false;
	}
}
