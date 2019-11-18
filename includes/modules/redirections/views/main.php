<?php
/**
 * Redirection main view.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Redirections
 */

use Classic_SEO\Helper;

$redirections = Helper::get_module( 'redirections' )->admin;
$redirections->table->prepare_items();

$is_new     = isset( $_GET['new'] );
$is_editing = ! empty( $_GET['url'] ) || ! empty( $_REQUEST['log'] ) || ! empty( $_REQUEST['redirect_uri'] ) || $redirections->form->is_editing();
?>
<div class="wrap cpseo-redirections-wrap">

	<h1 class="wp-heading-inline">
		<?php echo esc_html( get_admin_page_title() ); ?>
		<a class="cpseo-add-new-redirection<?php echo $is_editing ? '-refresh' : ''; ?> page-title-action" href="<?php echo Helper::get_admin_url( 'redirections', 'new=1' ); ?>"><?php esc_html_e( 'Add New', 'cpseo' ); ?></a>
		<a class="page-title-action" href="<?php echo Helper::get_admin_url( 'redirections', 'export=apache' ); ?>"><?php esc_html_e( 'Export to .htaccess', 'cpseo' ); ?></a>
		<a class="page-title-action" href="<?php echo Helper::get_admin_url( 'redirections', 'export=nginx' ); ?>"><?php esc_html_e( 'Export to Nginx config file', 'cpseo' ); ?></a>
		<a class="page-title-action" href="<?php echo Helper::get_admin_url( 'options-general#setting-panel-redirections' ); ?>"><?php esc_html_e( 'Settings', 'cpseo' ); ?></a>
	</h1>

	<div class="clear"></div>

	<div class="cpseo-redirections-form<?php echo $is_editing || $is_new ? ' is-editing' : ''; ?>">

		<?php $redirections->form->display(); ?>

	</div>

	<form method="get">
		<input type="hidden" name="page" value="cpseo-redirections">
		<?php $redirections->table->search_box( esc_html__( 'Search', 'cpseo' ), 's' ); ?>
	</form>

	<form method="post">
	<?php
		$redirections->table->views();
		$redirections->table->display();
	?>
	</form>

</div>
