<?php
/**
 * Import/Export page template.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin
 */

?>
<div class="wrap cpseo-wrap limit-wrap">

	<div class="cpseo-ui">
	
		<span class="wp-header-end"></span>

		<h1 class="page-title"><?php esc_html_e( 'Import &amp; Export', 'cpseo' ); ?></h1>

		<p style="font-size: 1rem;">
			<?php
			printf( esc_html__( 'Import your previous backed up setting. Or, Export your Classic SEO settings and meta data for backup or for reuse on a different site.', 'cpseo' ) );
			?>
		</p>

		<div class="col">

			<?php include_once 'export-panel.php'; ?>

			<?php include_once 'import-panel.php'; ?>

		</div>

		<div class="col">

			<?php include_once 'backup-panel.php'; ?>

			<?php include_once 'plugins-panel.php'; ?>

		</div>

	</div>

</div>
