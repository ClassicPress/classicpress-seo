<?php
/**
 * Help page template.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin
 */

/**
 * Add new help content tabs on help and support page.
 *
 * @param array $help_content
 */
$tabs = apply_filters( 'cpseo/help/tabs', array(
	'getting-started' => array(
		'title' => esc_html__( 'Getting started', 'cpseo' ),
		'view'  => 'help/getting-started.php',
	),
	'local-seo'       => array(
		'title' => esc_html__( 'Local SEO', 'cpseo' ),
		'view'  => 'help/local-seo.php',
	),
	'developers'       => array(
		'title' => esc_html__( 'Developers', 'cpseo' ),
		'view'  => 'help/developers.php',
	),
));
?>

<div class="wrap cpseo-wrap limit-wrap">

	<span class="wp-header-end"></span>

	<h1 class="page-title"><?php esc_html_e( 'Help &amp; Support', 'cpseo' ); ?></h1>
	<br>

	<div id="cpseo-help-wrapper" class="cpseo-tabs">

		<div class="cpseo-tabs-navigation wp-clearfix">
			<?php foreach ( $tabs as $id => $tab ) : ?>
			<a href="#help-panel-<?php echo $id; ?>"><?php echo $tab['title']; ?></a>
			<?php endforeach; ?>
		</div>

		<div class="cpseo-tabs-content">
			<?php foreach ( $tabs as $id => $tab ) : ?>
			<div id="help-panel-<?php echo $id; ?>" class="cpseo-tab">
				<?php include $tab['view']; ?>
			</div>
			<?php endforeach; ?>
		</div>

	</div>

</div>

