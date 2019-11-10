<?php
/**
 * SEO Status admin page contents.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 */

use Classic_SEO\Helper;
use Classic_SEO\Helpers\Param;

$module  = Helper::get_module( 'status' );
$current = Param::get( 'view', 'status' );
?>
<div class='wrap cpseo-status-wrap'>

	<span class='wp-header-end'></span>

	<h1 class="page-title"><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php $module->display_nav(); ?>

	<?php $module->display_body( $current ); ?>

</div>
