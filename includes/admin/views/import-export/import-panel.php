<?php
/**
 * Import panel template.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin
 */

?>
<form id="cpseo-import-form" class="cpseo-export-form cmb2-form" action="" method="post" enctype="multipart/form-data" accept-charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">

	<h3><?php esc_html_e( 'Import Settings', 'cpseo' ); ?></h3>

	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="import-me"><?php esc_html_e( 'Setting File', 'cpseo' ); ?></label>
				</th>
				<td>
					<input type="file" name="import-me" id="import-me" value="">
					<br>
					<span class="validation-message"><?php esc_html_e( 'Please select a file to import.', 'cpseo' ); ?></span>
					<p class="description"><?php esc_html_e( 'Import settings by locating setting file and clicking "Import settings".', 'cpseo' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>

	<footer>
		<?php wp_nonce_field( 'cpseo-import-settings' ); ?>
		<input type="hidden" name="object_id" value="import-plz">
		<input type="hidden" name="action" value="wp_handle_upload">
		<button type="submit" class="button button-primary button-xlarge"><?php esc_html_e( 'Import', 'cpseo' ); ?></button>
	</footer>
</form>
