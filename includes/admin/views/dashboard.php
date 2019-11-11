<?php
/**
 * Dashboard page template.
 *
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\Admin
 */

use ClassicPress_SEO\Admin\Admin_Helper;
use ClassicPress_SEO\Admin\System_Info;

$is_network_admin  = is_network_admin();
$is_network_active = ClassicPress_SEO\Helper::is_plugin_active_for_network();
$current_tab       = $is_network_active && $is_network_admin ? 'help' : ( isset( $_GET['view'] ) ? filter_input( INPUT_GET, 'view' ) : 'modules' );
?>

<div class="cpseo-wrap limit-wrap">

	<?php echo $this->cpseo_admin_header('general'); ?>
	
	<div id="cpseo-content" class="cpseo-option general">
		<div id="cpseo-tabs" class="wrap">

			<?php
			// phpcs:disable
			// Display modules activation and deactivation form.
			if ( 'modules' === $current_tab ) {
				cpseo()->manager->display_form();
			} else {
				include_once Admin_Helper::get_view( "dashboard-{$current_tab}" );
			}
			// phpcs:enable
			?>

		</div>
	</div>
</div>
