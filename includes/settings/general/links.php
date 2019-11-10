<?php
/**
 * The images settings.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Settings
 */

use Classic_SEO\Helper;

$cmb->add_field([
	'id'      => 'cpseo_strip_category_base',
	'type'    => 'switch',
	'name'    => esc_html__( 'Strip Category Base', 'cpseo' ),
	/* translators: Link to kb article */
	'desc'    => sprintf( wp_kses_post( __( 'Remove /category/ from category archive URLs. <br>E.g. <code>example.com/category/my-category/</code> becomes <code>example.com/my-category</code>', 'cpseo' ) ) ),
	'default' => 'off',
]);

$redirection_message = Helper::is_module_active( 'redirections' ) ?
	/* translators: Redirection page url */
	' <a href="' . Helper::get_admin_url( 'options-general#setting-panel-redirections' ) . '" target="new">' . esc_html__( 'Redirection Manager', 'cpseo' ) . '</a>' :
	'<span class="cpseo-tooltip">' . esc_html__( 'Redirections Manager', 'cpseo' ) . '<span>' . esc_html__( 'Please enable Redirections module.', 'cpseo' ) . '</span></span>';


$cmb->add_field([
	'id'      => 'cpseo_attachment_redirect_urls',
	'type'    => 'switch',
	'name'    => esc_html__( 'Redirect Attachments', 'cpseo' ),
	/* translators: Link to kb article */
	'desc'    => sprintf( wp_kses_post( __( 'Redirect all attachment page URLs to the post they appear in. <strong>It is strongly recommended you leave this setting to ON</strong>.', 'cpseo' ) ) ),
	'default' => 'on',
]);

$cmb->add_field([
	'id'      => 'cpseo_attachment_redirect_default',
	'type'    => 'text',
	'name'    => esc_html__( 'Redirect Orphan Media', 'cpseo' ),
	'desc'    => esc_html__( 'Redirect attachments without a parent post to this URL. Leave empty for no redirection.', 'cpseo' ),
	'default' => get_home_url(),
	'dep'     => [ [ 'cpseo_attachment_redirect_urls', 'on' ] ],
]);

$cmb->add_field([
	'id'      => 'cpseo_url_strip_stopwords',
	'type'    => 'switch',
	'name'    => esc_html__( 'Remove Stopwords from Permalinks', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'Remove stopwords such as <strong>a, and, the</strong>, etc. from permalinks. This option will only affect the auto-generated URLs for newly created posts and pages.', 'cpseo' ) ),
	'default' => 'off',
]);

$cmb->add_field([
	'id'   => 'cpseo_stopwords',
	'type' => 'textarea_small',
	'name' => esc_html__( 'Stopwords List', 'cpseo' ),
	'desc' => esc_html__( 'List of stopwords to remove. Add one per line.', 'cpseo' ),
	'dep'  => [ [ 'cpseo_url_strip_stopwords', 'on' ] ],
]);

$cmb->add_field([
	'id'      => 'cpseo_nofollow_external_links',
	'type'    => 'switch',
	'name'    => esc_html__( 'Nofollow External Links', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'Automatically add <code>rel="nofollow"</code> attribute for external links appearing in your posts, pages, and other post types. The attribute is dynamically applied when the content is displayed, and the stored content is not changed.', 'cpseo' ) ),
	'default' => 'off',
]);

$cmb->add_field([
	'id'      => 'cpseo_nofollow_image_links',
	'type'    => 'switch',
	'name'    => esc_html__( 'Nofollow Image File Links', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'Automatically add <code>rel="nofollow"</code> attribute for links pointing to external image files. The attribute is dynamically applied when the content is displayed, and the stored content is not changed.', 'cpseo' ) ),
	'default' => 'off',
]);

$cmb->add_field([
	'id'   => 'cpseo_nofollow_domains',
	'type' => 'textarea_small',
	'name' => esc_html__( 'Nofollow Domains', 'cpseo' ),
	'desc' => wp_kses_post( __( 'Only add <code>nofollow</code> attribute for the link if target domain is in this list. Add one per line. Leave empty to apply nofollow for <strong>ALL</strong> external domains.', 'cpseo' ) ),
	'dep'  => [
		[ 'cpseo_nofollow_external_links', 'on' ],
		[ 'cpseo_nofollow_image_links', 'on' ],
	],
]);

$cmb->add_field([
	'id'   => 'cpseo_nofollow_exclude_domains',
	'type' => 'textarea_small',
	'name' => esc_html__( 'Nofollow Exclude Domains', 'cpseo' ),
	'desc' => wp_kses_post( __( 'The <code>nofollow</code> attribute <strong>will not be added</strong> for the link if target domain is in this list. Add one per line.', 'cpseo' ) ),
	'dep'  => [
		[ 'cpseo_nofollow_external_links', 'on' ],
		[ 'cpseo_nofollow_image_links', 'on' ],
	],
]);

$cmb->add_field([
	'id'      => 'cpseo_new_window_external_links',
	'type'    => 'switch',
	'name'    => esc_html__( 'Open External Links in New Tab/Window', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'Automatically add <code>target="_blank"</code> attribute for external links appearing in your posts, pages, and other post types to make them open in a new browser tab or window. The attribute is dynamically applied when the content is displayed, and the stored content is not changed.', 'cpseo' ) ),
	'default' => 'on',
]);
