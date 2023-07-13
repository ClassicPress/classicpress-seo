<?php
/**
 * The Status module.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO
 */

namespace Classic_SEO\Status;

use Classic_SEO\Helper;
use Classic_SEO\Module\Base;
use Classic_SEO\Admin\Page;
use Classic_SEO\Helpers\Param;
use Classic_SEO\Helpers\Conditional;

defined( 'ABSPATH' ) || exit;

/**
 * Status class.
 */
#[\AllowDynamicProperties]
class Status extends Base {

	use Conditional;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		if ( Status::is_heartbeat() ) {
			return;
		}

		if ( Status::is_rest() ) {
			$tools = $this->get_page_views();
			$tools = new $tools['tools']['class'];
			$tools->hooks();
			return;
		}

		$directory = dirname( __FILE__ );
		$this->config(
			[
				'id'        => 'status',
				'directory' => $directory,
			]
		);

		parent::__construct();
	}

	/**
	 * Load the REST API endpoints.
	 */
	public function init_rest_api() {
		\error_log( 'coming' );
	}

	/**
	 * Register admin page.
	 */
	public function register_admin_page() {
		$uri = untrailingslashit( plugin_dir_url( __FILE__ ) );

		$this->page = new Page(
			'cpseo-status',
			esc_html__( 'Status & Tools', 'cpseo' ),
			[
				'position' => 12,
				'parent'   => 'cpseo',
				'classes'  => [ 'cpseo-page' ],
				'render'   => $this->directory . '/views/main.php',
				'assets'   => [
					'styles'  => [
						'cpseo-common' => '',
						'cpseo-status' => $uri . '/assets/status.css',
					],
					'scripts' => [
						'cpseo-status' => $uri . '/assets/status.js',
					],
				],
			]
		);
	}

	/**
	 * Display dashabord tabs.
	 */
	public function display_nav() {
		?>
		<h2 class="nav-tab-wrapper">
			<?php
			foreach ( $this->get_page_views() as $id => $link ) :
				if ( isset( $link['cap'] ) && ! current_user_can( $link['cap'] ) ) {
					continue;
				}
				?>
			<a class="nav-tab<?php echo Param::get( 'view', 'status' ) === sanitize_html_class( $id ) ? ' nav-tab-active' : ''; ?>" href="<?php echo esc_url( Helper::get_admin_url( $link['url'], $link['args'] ) ); ?>" title="<?php echo esc_attr( $link['title'] ); ?>"><?php echo esc_html( $link['title'] ); ?></a>
			<?php endforeach; ?>
		</h2>
		<?php
	}

	/**
	 * Display view body.
	 *
	 * @param string $view Current view.
	 */
	public function display_body( $view ) {
		$hash = $this->get_page_views();
		$hash = new $hash[ $view ]['class'];
		$hash->display();
	}

	/**
	 * Get dashbaord navigation links
	 *
	 * @return array
	 */
	private function get_page_views() {
		return [
			'status' => [
				'url'   => 'status',
				'args'  => '',
				'cap'   => 'manage_options',
				'title' => __( 'System Status', 'cpseo' ),
				'class' => '\\Classic_SEO\\Status\\System_Status',
			],
			'tools'  => [
				'url'   => 'status',
				'args'  => 'view=tools',
				'cap'   => 'manage_options',
				'title' => __( 'Tools', 'cpseo' ),
				'class' => '\\Classic_SEO\\Status\\Tools',
			],
		];
	}
}
