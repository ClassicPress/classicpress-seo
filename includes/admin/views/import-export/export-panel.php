<?php
/**
 * Export panel template.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin
 */

?>
<form class="cpseo-export-form cmb2-form" action="" method="post">

	<h3><?php esc_html_e( 'Export Settings', 'cpseo' ); ?></h3>

	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label for="status"><?php esc_html_e( 'Panels', 'cpseo' ); ?></label></th>
				<td>
					<ul class="cmb2-checkbox-list no-select-all cmb2-list">
						<li><input type="checkbox" class="cmb2-option" name="panels[]" id="status1" value="general" checked="checked"> <label for="status1"><?php esc_html_e( 'General Settings', 'cpseo' ); ?></label></li>
						<li><input type="checkbox" class="cmb2-option" name="panels[]" id="status2" value="titles" checked="checked"> <label for="status2"><?php esc_html_e( 'Titles &amp; Metas', 'cpseo' ); ?></label></li>
						<li><input type="checkbox" class="cmb2-option" name="panels[]" id="status3" value="sitemap" checked="checked"> <label for="status3"><?php esc_html_e( 'Sitemap Settings', 'cpseo' ); ?></label></li>
						<li><input type="checkbox" class="cmb2-option" name="panels[]" id="status4" value="role-manager" checked="checked"> <label for="status4"><?php esc_html_e( 'Role Manager Settings', 'cpseo' ); ?></label></li>
						<li><input type="checkbox" class="cmb2-option" name="panels[]" id="status5" value="redirections" checked="checked"> <label for="status5"><?php esc_html_e( 'Redirections', 'cpseo' ); ?></label></li>
					</ul>
					<p class="description"><?php esc_html_e( 'Choose the settings to export.', 'cpseo' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>

	<footer>
		<?php wp_nonce_field( 'cpseo-export-settings' ); ?>
		<input type="hidden" name="object_id" value="export-plz">
		<button type="submit" class="button button-primary button-xlarge"><?php esc_html_e( 'Export', 'cpseo' ); ?></button>
	</footer>

</form>
