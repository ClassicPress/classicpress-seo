<?php
/**
 * Sitemap - Authors
 *
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\Sitemap
 */

use ClassicPress_SEO\Helpers\WordPress;

$roles   = WordPress::get_roles();
$default = $roles;
unset( $default['administrator'], $default['editor'], $default['author'] );

$cmb->add_field( array(
	'id'                => 'cpseo_exclude_roles',
	'type'              => 'multicheck',
	'name'              => esc_html__( 'Exclude User Roles', 'cpseo' ),
	'desc'              => esc_html__( 'Selected roles will be excluded in the sitemap.', 'cpseo' ),
	'options'           => $roles,
	'default'           => $default,
	'select_all_button' => false,
) );

$cmb->add_field( array(
	'id'   => 'cpseo_exclude_users',
	'type' => 'text',
	'name' => esc_html__( 'Exclude Users', 'cpseo' ),
	'desc' => esc_html__( 'Add user IDs, separated by commas, to exclude them from the sitemap.', 'cpseo' ),
) );
