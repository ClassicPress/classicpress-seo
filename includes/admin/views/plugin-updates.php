<?php
/**
 * Plugin updates template.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin
 */

use Classic_SEO\Helper;

$current_version = CPSEO_VERSION;
$latest_version  = '0.0.5';
$is_updateable   = version_compare( $current_version, $latest_version, '<' );
$class           = $is_updateable ? 'status-red' : 'status-green';
?>
<div class="cpseo-box <?php echo $class; ?>">

	<div class="cpseo-box--title">

		<h4><?php esc_html_e( 'Plugin Updates', 'cpseo' ); ?></h4>

		<span class="cpseo-box--title-button <?php echo $class; ?>"><?php echo $is_updateable ? esc_html__( 'Update Available', 'cpseo' ) : esc_html__( 'Plugin up to date', 'cpseo' ); ?></span>

	</div>

	<div class="cpseo-box--content">

		<strong><?php esc_html_e( 'Installed Version', 'cpseo' ); ?></strong><br /><?php echo $current_version; ?>
		<br /><br />
		<strong><?php esc_html_e( 'Latest Available Version', 'cpseo' ); ?></strong><br /><?php echo $latest_version; ?>
		<br /><br /><br />
		<a class="button" href="<?php echo esc_url( Helper::get_admin_url( '', 'checkforupdates=true' ) ); ?>"><?php esc_html_e( 'Check for Updates', 'cpseo' ); ?></a>
		<p>&nbsp;</p><p>&nbsp;</p>
	</div>

</div>
