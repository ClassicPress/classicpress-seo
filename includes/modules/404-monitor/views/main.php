<?php
/**
 * Main template for 404 monitor
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Monitor
 */

use Classic_SEO\Helper;


$monitor = Helper::get_module( '404-monitor' )->admin;
$monitor->table->prepare_items();
?>
<div class="wrap cpseo-404-monitor-wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<style>
	#doaction, #doaction2 { margin: 0 }
	.column-times_accessed { width: 14% }
	.cpseo-clear-logs { color: #a00 !important; margin-bottom: 1px !important}
	.cpseo-clear-logs:hover { border-color: #a00 !important }
	</style>
	<p>
		<?php printf( __( 'Find broken links with the 404 monitor tool.', 'cpseo' ) ); ?>
	</p>
	<form method="get">
		<input type="hidden" name="page" value="cpseo-404-monitor">
		<?php $monitor->table->search_box( esc_html__( 'Search', 'cpseo' ), 's' ); ?>
	</form>
	<form method="post">
		<?php $monitor->table->display(); ?>
	</form>

</div>
