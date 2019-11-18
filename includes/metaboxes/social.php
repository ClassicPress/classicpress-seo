<?php
/**
 * Metabox - Social Tab
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Metaboxes
 */

use Classic_SEO\Helper;
use Classic_SEO\Admin\Admin_Helper;

$cmb->add_field( array(
	'id'   => 'cpseo_social_tabs',
	'type' => 'raw',
	'file' => cpseo()->includes_dir() . 'metaboxes/social-preview.php',
) );

/**
 * Facebook data.
 */
$cmb->add_field( array(
	'name'    => esc_html__( 'Panel', 'cpseo' ),
	'id'      => 'setting-panel-social-tab-content-start',
	'type'    => 'raw',
	'content' => '<div class="cpseo-tabs-content cpseo-custom">',
) );

$cmb->add_field( array(
	'name' => esc_html__( 'Panel', 'cpseo' ),
	'id'   => 'setting-panel-social-facebook',
	'type' => 'tab_open',
) );

$cmb->add_field( array(
	'id'      => 'cpseo_facebook_image',
	'type'    => 'file',
	'name'    => esc_html__( 'Image', 'cpseo' ),
	'options' => array( 'url' => false ),
	'text'    => array( 'add_upload_file_text' => esc_html__( 'Add Image', 'cpseo' ) ),
	'desc'    => esc_html__( 'Upload at least 600x315px image. Recommended size is 1200x630px.', 'cpseo' ),
	'after'   => '<div class="notice notice-warning inline hidden"><p>' . esc_html__( 'Image is smaller than the minimum size, please select a different image.', 'cpseo' ) . '</p></div>',
) );

$cmb->add_field( array(
	'id'         => 'cpseo_facebook_title',
	'type'       => 'text',
	'name'       => esc_html__( 'Title', 'cpseo' ),
	'attributes' => array( 'placeholder' => esc_html__( 'Classic_SEO -- Test Drive', 'cpseo' ) ),
) );

$cmb->add_field( array(
	'id'         => 'cpseo_facebook_description',
	'type'       => 'textarea',
	'name'       => esc_html__( 'Description', 'cpseo' ),
	'attributes' => array(
		'rows'            => 3,
		'data-autoresize' => true,
		'placeholder'     => esc_html__( 'Classic_SEO -- Description', 'cpseo' ),
	),
) );

if ( Admin_Helper::is_user_edit() ) {
	$cmb->add_field( array(
		'id'   => 'cpseo_facebook_author',
		'type' => 'text',
		'name' => esc_html__( 'Author Profile URL', 'cpseo' ),
		/* translators: option page link */
		'desc' => sprintf( wp_kses_post( __( 'Insert a Facebook profile URL to display author name when the page is shared on Facebook.<br>The author name will be clickable if the profile is set to allow public followers.<br>You can set up default URL for fallback in <a href="%s" target="_blank">SEO &raquo; Titles &amp; Meta &raquo; Social</a>.', 'cpseo' ) ), Helper::get_admin_url( 'options-titles#setting-panel-social' ) ),
	) );
}

$cmb->add_field( array(
	'id'   => 'setting-panel-social-facebook-close',
	'type' => 'tab_close',
) );

/**
 * Twitter data.
 */
$dep = array(
	array( 'cpseo_twitter_use_facebook', 'off' ),
);

$cmb->add_field( array(
	'name' => esc_html__( 'Panel', 'cpseo' ),
	'id'   => 'setting-panel-social-twitter',
	'type' => 'tab_open',
) );

$cmb->add_field( array(
	'id'      => 'cpseo_twitter_use_facebook',
	'type'    => 'switch',
	'name'    => esc_html__( 'Use Data from Facebook Tab', 'cpseo' ),
	'default' => 'on',
) );

$card_type = array(
	'summary_large_image' => esc_html__( 'Summary Card with Large Image', 'cpseo' ),
	'summary_card'        => esc_html__( 'Summary Card', 'cpseo' ),
);

$cmb->add_field( array(
	'id'      => 'cpseo_twitter_card_type',
	'type'    => 'select',
	'name'    => esc_html__( 'Card type', 'cpseo' ),
	'options' => $card_type,
	'default' => Helper::get_settings( 'titles.cpseo_twitter_card_type' ),
) );

$basic   = array( 'relation' => 'and' ) + $dep;

$cmb->add_field( array(
	'id'      => 'cpseo_twitter_image',
	'type'    => 'file',
	'name'    => esc_html__( 'Image', 'cpseo' ),
	'options' => array( 'url' => false ),
	'text'    => array( 'add_upload_file_text' => esc_html__( 'Add Image', 'cpseo' ) ),
	'dep'     => $basic,
	'desc'    => esc_html__( 'Images for this Card support an aspect ratio of 2:1 with minimum dimensions of 300x157 or maximum of 4096x4096 pixels. Images must be less than 5MB in size.', 'cpseo' ),
	'after'   => '<div class="notice notice-warning inline hidden"><p>' . esc_html__( 'Image is smaller than the minimum size, please select a different image.', 'cpseo' ) . '</p></div>',
) );

$cmb->add_field( array(
	'id'         => 'cpseo_twitter_title',
	'type'       => 'text',
	'name'       => esc_html__( 'Title', 'cpseo' ),
	'attributes' => array( 'placeholder' => esc_html__( 'Classic_SEO -- Test Drive', 'cpseo' ) ),
	'dep'        => $basic,
) );

$cmb->add_field( array(
	'id'         => 'cpseo_twitter_description',
	'type'       => 'textarea',
	'name'       => esc_html__( 'Description', 'cpseo' ),
	'attributes' => array(
		'rows'            => 3,
		'data-autoresize' => true,
	),
	'dep'        => $basic,
) );

// Image overlay fields.
$img_overlay   = array( 'relation' => 'and' ) + $dep;

if ( Admin_Helper::is_user_edit() ) {
	$cmb->add_field( array(
		'id'   => 'cpseo_twitter_author',
		'type' => 'text',
		'name' => esc_html__( 'Author Profile URL', 'cpseo' ),
		/* translators: option page link */
		'desc' => sprintf( wp_kses_post( __( 'Insert Twitter username to add twitter:creator tag to posts when the page is shared on Twitter.<br>You can set up default URL for fallback in <a href="%s" target="_blank">SEO &raquo; Titles &amp; Meta &raquo; Social</a>.', 'cpseo' ) ), Helper::get_admin_url( 'options-titles#setting-panel-social' ) ),
	) );
}

$cmb->add_field( array(
	'id'   => 'setting-panel-social-twitter-close',
	'type' => 'tab_close',
) );

$cmb->add_field( array(
	'name'    => esc_html__( 'Panel', 'cpseo' ),
	'id'      => 'setting-panel-social-tab-content-end',
	'type'    => 'raw',
	'content' => '</div> <!-- ./cpseo-tabs-content -->
	</div> <!-- ./cpseo-tabs -->',
) );
