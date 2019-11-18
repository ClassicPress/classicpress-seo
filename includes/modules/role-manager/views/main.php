<?php
/**
 * Help Role page template.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Role_Manager
 */

?>
<div class="wrap cpseo-wrap limit-wrap">

	<span class="wp-header-end"></span>

	<form class="cmb-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">

		<header>
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p>
				<?php
					/* translators: %s is a Learn More link to the documentation */
					printf( __( 'Control access to Classic SEO settings.', 'cpseo' ) );
				?>
			</p>
		</header>

		<input type="hidden" name="action" value="cpseo_save_capabilities">
		<?php
			wp_nonce_field( 'cpseo-save-capabilities', 'security' );
			$cmb = cmb2_get_metabox( 'cpseo-role-manager', 'cpseo-role-manager' );
			$cmb->show_form();
		?>

		<footer class="form-footer cpseo-ui">
			<button type="submit" class="button button-primary button-xlarge"><?php esc_html_e( 'Update Capabilities', 'cpseo' ); ?></button>
		</footer>

	</form>

</div>
