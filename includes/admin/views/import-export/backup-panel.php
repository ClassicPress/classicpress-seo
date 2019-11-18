<?php
/**
 * Backup panel template.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin
 */

$backups = get_option( 'cpseo_backups', [] );
?>
<div class="cpseo-export-form cmb2-form">

	<button type="button" class="button button-primary alignright cpseo-action" data-action="createBackup"><?php esc_html_e( 'Create Backup', 'cpseo' ); ?></button>

	<h3><?php esc_html_e( 'Backups', 'cpseo' ); ?></h3>

	<div class="list-table with-action">
		<table class="form-table">
			<tbody>
				<?php foreach ( $backups as $key => $backup ) : ?>
					<tr>
						<th>
							<?php
							/* translators: Backup formatted date */
							printf( esc_html__( 'Backup: %s', 'cpseo' ), date_i18n( 'M jS Y, H:i a', $key ) );
							?>
						</th>
						<td style="width:195px;padding-left:0;">
							<button type="button" class="button button-primary cpseo-action" data-action="restoreBackup" data-key="<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Restore', 'cpseo' ); ?></button>
							<button type="button" class="button button-link-delete cpseo-action" data-action="deleteBackup" data-key="<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Delete', 'cpseo' ); ?></button>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php if ( empty( $backups ) ) : ?>
					<tr class="hidden">
						<th>
						</th>
						<td style="width:195px;padding-left:0;">
							<button type="button" class="button button-primary cpseo-action" data-action="restoreBackup" data-key=""><?php esc_html_e( 'Restore', 'cpseo' ); ?></button>
							<button type="button" class="button button-link-delete cpseo-action" data-action="deleteBackup" data-key=""><?php esc_html_e( 'Delete', 'cpseo' ); ?></button>
						</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

	<p id="cpseo-no-backup-message"<?php echo ! empty( $backups ) ? ' class="hidden"' : ''; ?>><?php esc_html_e( 'There is no backup.', 'cpseo' ); ?></p>

</div>
