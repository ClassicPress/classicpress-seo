<?php
/**
 * Developers tab.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin\Help
 */


?>

<h3><?php esc_html_e( 'Filters and Hooks', 'cpseo' ); ?></h3>


<p><?php esc_html_e( 'There are a number of hooks and filters in Classic SEO that developers can use to interact with data output by Classic SEO. Here\'s a few of them.', 'cpseo' ); ?></p>

<h4><?php esc_html_e( 'Filter to show/hide SEO Metabox', 'cpseo' ); ?></h4>
<p><?php esc_html_e( 'To hide the SEO metabox, return false.', 'cpseo' ); ?></p>
<?php
$code1 = 'add_filter( \'cpseo/metabox/add_seo_metabox\', function( $default ) {
  return $default;
});';
?>
<p><pre><code><?php esc_html_e($code1); ?></code></pre></p>

<hr>

<h4><?php esc_html_e( 'Filter to change the position of the SEO metabox', 'cpseo' ); ?></h4>
<p><?php esc_html_e( 'Options for $priority are: \'low\', \'default\', \'core\', \'high\'.', 'cpseo' ); ?></p>
<?php
$code2 = 'add_filter( \'cpseo/metabox/priority\', function( $priority ) {
  return $priority;
});';
?>
<p><pre><code><?php esc_html_e($code2); ?></code></pre></p>

<hr>

<h4><?php esc_html_e( 'Filter to change taxonomy icons in the options panel', 'cpseo' ); ?></h4>
<p><?php esc_html_e( 'This example sets the icon for the custom \'event_cat\' taxonomy to \'dashicons dashicons-category\'.', 'cpseo' ); ?></p>
<?php
$code3 = 'add_filter( \'cpseo/taxonomy_icons\', function( $icons ) {
	$icons[\'event_cat\'] = \'dashicons dashicons-category\';
	return $icons;
});';
?>
<p><pre><code><?php esc_html_e($code3); ?></code></pre></p>

<hr>

<h4><?php esc_html_e( 'Filter to change taxonomy icons in the options panel', 'cpseo' ); ?></h4>
<p><?php esc_html_e( 'This example sets the icon for the custom \'event\' post type to \'dashicons dashicons-calendar\'.', 'cpseo' ); ?></p>
<?php
$code4 = 'add_filter( \'cpseo/post_type_icons\', function( $icons ) {
	$icons[\'event\'] = \'dashicons dashicons-calendar\';
	return $icons;
});';
?>
<p><pre><code><?php esc_html_e($code4); ?></code></pre></p>
