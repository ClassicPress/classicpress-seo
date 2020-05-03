<?php
/**
 * The post type settings.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Settings
 */

use Classic_SEO\Helper;

$post_type     = $tab['post_type'];
$post_type_obj = get_post_type_object( $post_type );
$name          = $post_type_obj->labels->singular_name;

$custom_default  = 'off';
$richsnp_default = [
	'post'    => 'article',
	'product' => 'product',
];

if ( 'post' === $post_type || 'page' === $post_type ) {
	$custom_default = 'off';
} elseif ( 'attachment' === $post_type ) {
	$custom_default = 'on';
}

$primary_taxonomy_hash = [
	'post'    => 'category',
	'product' => 'product_cat',
];

$cmb->add_field([
	'id'              => 'cpseo_pt_' . $post_type . '_title',
	'type'            => 'text',
	/* translators: post type name */
	'name'            => sprintf( esc_html__( 'Single %s Title', 'cpseo' ), $name ),
	/* translators: post type name */
	'desc'            => sprintf( esc_html__( 'Default title tag for single %s pages. This can be changed on a per-post basis on the post editor screen.', 'cpseo' ), $name ),
	'classes'         => 'cpseo-supports-variables cpseo-title',
	'default'         => '%title% %page% %sep% %sitename%',
	'attributes'      => [ 'data-exclude-variables' => 'seo_title,seo_description' ],
	'sanitization_cb' => [ '\Classic_SEO\CMB2', 'sanitize_textfield' ],
]);

$cmb->add_field([
	'id'              => 'cpseo_pt_' . $post_type . '_description',
	'type'            => 'textarea_small',
	/* translators: post type name */
	'name'            => sprintf( esc_html__( 'Single %s Description', 'cpseo' ), $name ),
	/* translators: post type name */
	'desc'            => sprintf( esc_html__( 'Default description for single %s pages. This can be changed on a per-post basis on the post editor screen.', 'cpseo' ), $name ),
	'classes'         => 'cpseo-supports-variables cpseo-description',
	'sanitization_cb' => true,
	'attributes'      => [
		'class'             => 'cmb2-textarea-small wp-exclude-emoji',
		'data-gramm_editor' => 'false',
		'data-exclude-variables' => 'seo_title,seo_description',
	],
]);

$cmb->add_field([
	'id'              => 'cpseo_pt_' . $post_type . '_archive_title',
	'type'            => 'text',
	/* translators: post type name */
	'name'            => sprintf( esc_html__( '%s Archive Title', 'cpseo' ), $name ),
	/* translators: post type name */
	'desc'            => sprintf( esc_html__( 'Title for %s archive pages.', 'cpseo' ), $name ),
	'classes'         => 'cpseo-supports-variables cpseo-title',
	'default'         => '%title% %page% %sep% %sitename%',
	'sanitization_cb' => false,
	'attributes'      => [ 'data-exclude-variables' => 'seo_title,seo_description' ],
]);

$cmb->add_field([
	'id'              => 'cpseo_pt_' . $post_type . '_archive_description',
	'type'            => 'textarea_small',
	/* translators: post type name */
	'name'            => sprintf( esc_html__( '%s Archive Description', 'cpseo' ), $name ),
	/* translators: post type name */
	'desc'            => sprintf( esc_html__( 'Description for %s archive pages.', 'cpseo' ), $name ),
	'classes'         => 'cpseo-supports-variables cpseo-description',
	'sanitization_cb' => false,
	'attributes'      => [ 'data-exclude-variables' => 'seo_title,seo_description' ],
]);

if ( 'product' === $post_type || 'download' === $post_type ) {

	$cmb->add_field([
		'id'      => 'cpseo_pt_' . $post_type . '_default_rich_snippet',
		'type'    => 'radio_inline',
		'name'    => esc_html__( 'Rich Snippet Type', 'cpseo' ),
		/* translators: link to title setting screen */
		'desc'    => __( 'Default rich snippet selected when creating a new product.', 'cpseo' ),
		'options' => [
			'off'     => esc_html__( 'None', 'cpseo' ),
			'product' => esc_html__( 'Product', 'cpseo' ),
		],
		'default' => $this->do_filter( 'settings/snippet/type', 'product', $post_type ),
	]);

} else {
	$cmb->add_field([
		'id'      => 'cpseo_pt_' . $post_type . '_default_rich_snippet',
		'type'    => 'select',
		'name'    => esc_html__( 'Rich Snippet Type', 'cpseo' ),
		'desc'    => esc_html__( 'Default rich snippet selected when creating a new post of this type. ', 'cpseo' ),
		'options' => Helper::choices_rich_snippet_types( esc_html__( 'None (Click here to set one)', 'cpseo' ) ),
		'default' => $this->do_filter( 'settings/snippet/type', isset( $richsnp_default[ $post_type ] ) ? $richsnp_default[ $post_type ] : 'off', $post_type ),
	]);

	// Common fields.
	$cmb->add_field([
		'id'              => 'cpseo_pt_' . $post_type . '_default_snippet_name',
		'type'            => 'text',
		'name'            => esc_html__( 'Headline', 'cpseo' ),
		'dep'             => [ [ 'cpseo_pt_' . $post_type . '_default_rich_snippet', 'off', '!=' ] ],
		'classes'         => 'cpseo-supports-variables',
		'default'         => '%title%',
		'sanitization_cb' => false,
	]);

	$cmb->add_field([
		'id'              => 'cpseo_pt_' . $post_type . '_default_snippet_desc',
		'type'            => 'textarea',
		'name'            => esc_html__( 'Description', 'cpseo' ),
		'attributes'      => [
			'class'           => 'cmb2_textarea wp-exclude-emoji',
			'rows'            => 3,
			'data-autoresize' => true,
		],
		'classes'         => 'cpseo-supports-variables',
		'default'         => '%seo_description%',
		'dep'             => [ [ 'cpseo_pt_' . $post_type . '_default_rich_snippet', 'off,book,local', '!=' ] ],
		'sanitization_cb' => false,
	]);
}

// Article fields.
$article_dep = [ [ 'cpseo_pt_' . $post_type . '_default_rich_snippet', 'article' ] ];
/* translators: Google article snippet doc link */
$article_desc = 'person' === Helper::get_settings( 'titles.cpseo_knowledgegraph_type' ) ? '<div class="notice notice-warning inline"><p>' . sprintf( __( 'Google does not allow Person as the Publisher for articles. Organization will be used instead.', 'cpseo' ) ) . '</p></div>' : '';
$cmb->add_field([
	'id'      => 'cpseo_pt_' . $post_type . '_default_article_type',
	'type'    => 'radio_inline',
	'name'    => esc_html__( 'Article Type', 'cpseo' ),
	'options' => [
		'Article'     => esc_html__( 'Article', 'cpseo' ),
		'BlogPosting' => esc_html__( 'Blog Post', 'cpseo' ),
		'NewsArticle' => esc_html__( 'News Article', 'cpseo' ),
	],
	'default' => $this->do_filter( 'settings/snippet/article_type', 'post' === $post_type ? 'BlogPosting' : 'Article', $post_type ),
	'desc'    => $article_desc,
	'dep'     => $article_dep,
]);

$cmb->add_field([
	'id'      => 'cpseo_pt_' . $post_type . '_custom_robots',
	'type'    => 'switch',
	/* translators: post type name */
	'name'    => sprintf( esc_html__( '%s Robots Meta', 'cpseo' ), $name ),
	/* translators: post type name */
	'desc'    => sprintf( wp_kses_post( __( 'Select custom robots meta, such as <code>nofollow</code>, <code>noarchive</code>, etc. for single %s pages. Otherwise the default meta will be used, as set in the Global Meta tab.', 'cpseo' ) ), $name ),
	'options' => [
		'off' => esc_html__( 'Default', 'cpseo' ),
		'on'  => esc_html__( 'Custom', 'cpseo' ),
	],
	'default' => $custom_default,
]);

$cmb->add_field([
	'id'                => 'cpseo_pt_' . $post_type . '_robots',
	'type'              => 'multicheck',
	/* translators: post type name */
	'name'              => sprintf( esc_html__( '%s Robots Meta', 'cpseo' ), $name ),
	'desc'              => esc_html__( 'Custom values for robots meta tag on homepage.', 'cpseo' ),
	'options'           => Helper::choices_robots(),
	'select_all_button' => false,
	'dep'               => [ [ 'cpseo_pt_' . $post_type . '_custom_robots', 'on' ] ],
]);

$cmb->add_field([
	'id'              => 'cpseo_pt_' . $post_type . '_advanced_robots',
	'type'            => 'advanced_robots',
	/* translators: post type name */
	'name'            => sprintf( esc_html__( '%s Advanced Robots Meta', 'cpseo' ), $name ),
	'sanitization_cb' => [ '\Classic_SEO\CMB2', 'sanitize_advanced_robots' ],
	'dep'             => [ [ 'cpseo_pt_' . $post_type . '_custom_robots', 'on' ] ],
]);

$cmb->add_field([
	'id'      => 'cpseo_pt_' . $post_type . '_link_suggestions',
	'type'    => 'switch',
	'name'    => esc_html__( 'Link Suggestions', 'cpseo' ),
	'desc'    => esc_html__( 'Enable Link Suggestions meta box for this post type, along with the Cornerstone Content feature.', 'cpseo' ),
	'default' => $this->do_filter( 'settings/titles/link_suggestions', 'on', $post_type ),
]);

$cmb->add_field([
	'id'      => 'cpseo_pt_' . $post_type . '_ls_use_fk',
	'type'    => 'radio_inline',
	'name'    => esc_html__( 'Link Suggestion Titles', 'cpseo' ),
	'desc'    => esc_html__( 'Use the Focus Keyword as the default text for the links instead of the post titles.', 'cpseo' ),
	'options' => [
		'titles'         => esc_html__( 'Titles', 'cpseo' ),
		'focus_keywords' => esc_html__( 'Focus Keywords', 'cpseo' ),
	],
	'default' => 'titles',
	'dep'     => [ [ 'cpseo_pt_' . $post_type . '_link_suggestions', 'on' ] ],
]);

$taxonomies = Helper::get_object_taxonomies( $post_type );
if ( $taxonomies ) {
	$cmb->add_field([
		'id'      => 'cpseo_pt_' . $post_type . '_primary_taxonomy',
		'type'    => 'select',
		'name'    => esc_html__( 'Primary Taxonomy', 'cpseo' ),
		/* translators: post type name */
		'desc'    => sprintf( esc_html__( 'Select taxonomy to show in the Breadcrumbs when a single %1$s is being viewed.', 'cpseo' ), $name ),
		'options' => $taxonomies,
		'default' => isset( $primary_taxonomy_hash[ $post_type ] ) ? $primary_taxonomy_hash[ $post_type ] : 'off',
	]);
}

$cmb->add_field([
	'id'   => 'cpseo_pt_' . $post_type . '_facebook_image',
	'type' => 'file',
	'name' => esc_html__( 'Thumbnail for Facebook', 'cpseo' ),
	'desc' => esc_html__( 'Image displayed when your page is shared on Facebook and other social networks. Use images that are at least 1200 x 630 pixels for the best display on high resolution devices.', 'cpseo' ),
]);

// Enable/Disable Metabox option.
if ( 'attachment' === $post_type ) {
	$cmb->add_field([
		'id'      => 'cpseo_pt_' . $post_type . '_bulk_editing',
		'type'    => 'radio_inline',
		'name'    => esc_html__( 'Bulk Editing', 'cpseo' ),
		'desc'    => esc_html__( 'Add bulk editing columns to the post listing screen.', 'cpseo' ),
		'options' => [
			'0'        => esc_html__( 'Disabled', 'cpseo' ),
			'editing'  => esc_html__( 'Enabled', 'cpseo' ),
			'readonly' => esc_html__( 'Read Only', 'cpseo' ),
		],
		'default' => 'editing',
	]);
} else {
	$cmb->add_field([
		'id'      => 'cpseo_pt_' . $post_type . '_add_meta_box',
		'type'    => 'switch',
		'name'    => esc_html__( 'Add SEO Meta Box', 'cpseo' ),
		'desc'    => esc_html__( 'Add the SEO Meta Box for the editor screen to customize SEO options for posts in this post type.', 'cpseo' ),
		'default' => 'on',
	]);

	$cmb->add_field([
		'id'      => 'cpseo_pt_' . $post_type . '_bulk_editing',
		'type'    => 'radio_inline',
		'name'    => esc_html__( 'Bulk Editing', 'cpseo' ),
		'desc'    => esc_html__( 'Add bulk editing columns to the post listing screen.', 'cpseo' ),
		'options' => [
			'0'        => esc_html__( 'Disabled', 'cpseo' ),
			'editing'  => esc_html__( 'Enabled', 'cpseo' ),
			'readonly' => esc_html__( 'Read Only', 'cpseo' ),
		],
		'default' => 'editing',
		'dep'     => [ [ 'cpseo_pt_' . $post_type . '_add_meta_box', 'on' ] ],
	]);
	
	$cmb->add_field([
		'id'      => 'cpseo_pt_' . $post_type . '_analyze_fields',
		'type'    => 'textarea_small',
		'name'    => esc_html__( 'Custom Fields', 'cpseo' ),
		'desc'    => esc_html__( 'List of custom fields name to include in the Page analysis. Add one per line.', 'cpseo' ),
		'default' => '',
	]);
}

// Archive not enabled.
if ( ! $post_type_obj->has_archive ) {
	$cmb->remove_field( 'cpseo_pt_' . $post_type . '_archive_title' );
	$cmb->remove_field( 'cpseo_pt_' . $post_type . '_archive_description' );
	$cmb->remove_field( 'cpseo_pt_' . $post_type . '_facebook_image' );
}

if ( 'attachment' === $post_type ) {
	$cmb->remove_field( 'cpseo_pt_' . $post_type . '_link_suggestions' );
	$cmb->remove_field( 'cpseo_pt_' . $post_type . '_ls_use_fk' );
}
