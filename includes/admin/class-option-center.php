<?php
/**
 * The option center of the plugin.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin
 */


namespace Classic_SEO\Admin;

use Classic_SEO\CMB2;
use Classic_SEO\Helper;
use Classic_SEO\Runner;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Helpers\Arr;
use Classic_SEO\Helpers\Str;
use Classic_SEO\Helpers\Param;
use Classic_SEO\Helpers\WordPress;

defined( 'ABSPATH' ) || exit;

/**
 * Option_Center class.
 */
class Option_Center implements Runner {

	use Hooker;

	/**
	 * Register hooks.
	 */
	public function hooks() {
		$this->action( 'init', 'register_general_settings', 125 );
		$this->action( 'init', 'register_title_settings', 125 );
		$this->filter( 'cpseo/settings/title', 'title_post_type_settings', 1 );
		$this->filter( 'cpseo/settings/title', 'title_taxonomy_settings', 1 );
		$this->filter( 'cpseo/settings/general', 'remove_unwanted_general_tabs', 1 );

		// Check for fields and act accordingly.
		$this->action( 'cmb2_save_options-page_fields_cpseo-options-general_options', 'check_updated_fields', 25, 2 );
		$this->action( 'cmb2_save_options-page_fields_cpseo-options-titles_options', 'check_updated_fields', 25, 2 );
	}

	/**
	 * General Settings.
	 */
	public function register_general_settings() {
		$tabs = [
			'links'       => [
				'icon'  => 'fa fa-link',
				'title' => esc_html__( 'Links', 'cpseo' ),
			],
			'images'      => [
				'icon'  => 'dashicons dashicons-images-alt2',
				'title' => esc_html__( 'Images', 'cpseo' ),
			],
			'breadcrumbs' => [
				'icon'  => 'fa fa-angle-double-right',
				'title' => esc_html__( 'Breadcrumbs', 'cpseo' ),
				'desc'  => sprintf( esc_html__( 'Use the following code in your theme template files to display breadcrumbs:', 'cpseo' ) ) . '<br /><code>&lt;?php if (function_exists(\'cpseo_the_breadcrumbs\')) cpseo_the_breadcrumbs(); ?&gt;</code><br /> OR <br /><code>[cpseo_breadcrumb]</code>',
			],
			'others'      => [
				'icon'  => 'fa fa-dot-circle-o',
				'title' => esc_html__( 'Misc', 'cpseo' ),
				'desc'  => sprintf( esc_html__( 'Plugin auto update and RSS settings.', 'cpseo' ) ),
			],
		];

		/**
		 * Allow developers to add new sections in the General Settings.
		 *
		 * @param array $tabs
		 */
		$tabs = $this->do_filter( 'settings/general', $tabs );

		new Options([
			'key'        => 'cpseo-options-general',
			'title'      => esc_html__( 'SEO Settings', 'cpseo' ),
			'menu_title' => esc_html__( 'General Settings', 'cpseo' ),
			'capability' => 'cpseo_general',
			'folder'     => 'general',
			'tabs'       => $tabs,
		]);
	}

	/**
	 * Remove unneeded tabs from the General Settings.
	 *
	 * @param  array $tabs Hold tabs for optional panel.
	 * @return array
	 */
	public function remove_unwanted_general_tabs( $tabs ) {
		if ( is_multisite() ) {
			unset( $tabs['robots'] );
		}

		return $tabs;
	}

	/**
	 * Register SEO Titles & Meta Settings.
	 */
	public function register_title_settings() {
		$tabs = [
			'global'   => [
				'icon'  => 'fa fa-cogs',
				'title' => esc_html__( 'Global Meta', 'cpseo' ),
				'desc'  => sprintf( esc_html__( 'SEO titles and meta options for all pages of your site.', 'cpseo' ) ),
			],
			'local'    => [
				'icon'  => 'fa fa-map-marker',
				'title' => esc_html__( 'Local SEO', 'cpseo' ),
				/* translators: Redirection page url */
				'desc'  => sprintf( wp_kses_post( __( 'Settings for contact information & opening hours of your local business.<br><br>Use the <code>[cpseo_contact_info]</code> shortcode to display contact information in a nicely formatted way. You should also claim your business on Google if you have not already.', 'cpseo' ) ) ),
			],
			'social'   => [
				'icon'  => 'fa fa-retweet',
				'title' => esc_html__( 'Social Meta', 'cpseo' ),
				'desc'  => sprintf( esc_html__( 'Settings for social networks and feeds. Social page URLs will be displayed in the contact shortcode and added to the pages as metadata to be displayed in Knowledge Graph cards. ', 'cpseo' ) ),
			],
			'homepage' => [
				'icon'  => 'fa fa-home',
				'title' => esc_html__( 'Homepage', 'cpseo' ),
				'desc'  => 'page' === get_option( 'show_on_front' ) ?
					/* translators: something */
					sprintf( wp_kses_post( __( 'A static page is configured in Settings &gt; Reading. To set up the title, description, and meta for the homepage, use the meta box in the page editor.<br><br><a href="%1$s">Edit Page: %2$s</a>', 'cpseo' ) ), admin_url( 'post.php?post=' . get_option( 'page_on_front' ) ) . '&action=edit', get_the_title( get_option( 'page_on_front' ) ) ) :
					sprintf( esc_html__( 'Change SEO options for homepage such as title and meta description .', 'cpseo' ) ),
			],
			'author'   => [
				'icon'  => 'fa fa-users',
				'title' => esc_html__( 'Authors', 'cpseo' ),
				'desc'  => sprintf( esc_html__( 'Change SEO options related to the author archive pages. Author archives list the posts from a particular author in chronological order.', 'cpseo' ) ),
			],
			'misc'     => [
				'icon'  => 'fa fa-map-signs',
				'title' => esc_html__( 'Misc Pages', 'cpseo' ),
				'desc'  => sprintf( esc_html__( 'Settings related to pages like search results, 404 error pages etc.', 'cpseo' ) ),
			],
		];

		/**
		 * Allow developers to add new section in the Title Settings.
		 *
		 * @param array $tabs
		 */
		$tabs = $this->do_filter( 'settings/title', $tabs );

		new Options([
			'key'        => 'cpseo-options-titles',
			'title'      => esc_html__( 'SEO Titles &amp; Meta', 'cpseo' ),
			'menu_title' => esc_html__( 'Titles &amp; Meta', 'cpseo' ),
			'capability' => 'cpseo_titles',
			'folder'     => 'titles',
			'tabs'       => $tabs,
		]);

		if ( is_admin() ) {
			Helper::add_json( 'postTitle', 'Post Title' );
			Helper::add_json( 'postUri', home_url( '/post-title' ) );
			Helper::add_json( 'blogName', get_bloginfo( 'name' ) );
		}
	}

	/**
	 * Add post type tabs in the Title Settings panel.
	 *
	 * @param  array $tabs Holds the tabs of the options panel.
	 * @return array
	 */
	public function title_post_type_settings( $tabs ) {
		$icons = Helper::choices_post_type_icons();

		$names = [
			'post'       => 'single %s',
			'page'       => 'single %s',
			'product'    => 'product pages',
			'attachment' => 'media %s',
		];

		$tabs['p_types'] = [
			'title' => esc_html__( 'Post Types:', 'cpseo' ),
			'type'  => 'seprator',
		];

		foreach ( Helper::get_accessible_post_types() as $post_type ) {
			$obj      = get_post_type_object( $post_type );
			$obj_name = isset( $names[ $obj->name ] ) ? sprintf( $names[ $obj->name ], $obj->name ) : $obj->name;
			$icon     = isset( $icons[ $obj->name ] ) ? $icons[ $obj->name ] : $icons['default'];

			// Set settings menu icon. Attempts to use the icon as defined by custom post type instead of default icon.
			if ( isset( $obj->menu_icon ) && $obj->menu_icon != '' ) {
				if ( Str::starts_with( 'dashicons', $obj->menu_icon ) ) {
					$icon = 'dashicons ' . $obj->menu_icon;	// dashicons
				}
				else if ( Str::starts_with( 'fa', $obj->menu_icon ) ) {
					$icon = 'fa ' . $obj->menu_icon;		//Font Awesome
				}
				else if ( Str::starts_with( 'http', $obj->menu_icon ) ) {
					$icon = $obj->menu_icon;				// Images
				}
			}


			$tabs[ 'post-type-' . $obj->name ] = [
				'title'     => $obj->label,
				'icon'      => $icon,
				/* translators: post type name */
				'desc'      => sprintf( esc_html__( 'SEO options for %s', 'cpseo' ), $obj_name),
				'post_type' => $obj->name,
				'file'      => cpseo()->includes_dir() . 'settings/titles/post-types.php',
			];
		}

		return $tabs;
	}

	/**
	 * Add taxonomy tabs in the Title Settings panel.
	 *
	 * @param  array $tabs Holds the tabs of the options panel.
	 * @return array
	 */
	public function title_taxonomy_settings( $tabs ) {
		$icons = Helper::choices_taxonomy_icons();

		$hash_name = [
			'category'    => 'category archive pages',
			'product_cat' => 'Product category pages',
			'product_tag' => 'Product tag pages',
		];

		foreach ( Helper::get_accessible_taxonomies() as $taxonomy ) {
			$attached = implode( ' + ', $taxonomy->object_type );

			// Separator.
			$tabs[ $attached ] = [
				'title' => ucwords( $attached ) . ':',
				'type'  => 'seprator',
			];

			$taxonomy_name = isset( $hash_name[ $taxonomy->name ] ) ? $hash_name[ $taxonomy->name ] : $taxonomy->label;

			$tabs[ 'taxonomy-' . $taxonomy->name ] = [
				'icon'     => isset( $icons[ $taxonomy->name ] ) ? $icons[ $taxonomy->name ] : $icons['default'],
				'title'    => $taxonomy->label,
				/* translators: taxonomy name */
				'desc'     => sprintf( esc_html__( 'SEO options for %s.', 'cpseo' ), $taxonomy_name ),
				'taxonomy' => $taxonomy->name,
				'file'     => cpseo()->includes_dir() . 'settings/titles/taxonomies.php',
			];
		}

		if ( isset( $tabs['taxonomy-post_format'] ) ) {
			$tab = $tabs['taxonomy-post_format'];
			unset( $tabs['taxonomy-post_format'] );
			$tab['title']      = esc_html__( 'Post Formats', 'cpseo' );
			$tab['page_title'] = esc_html__( 'Post Formats Archive', 'cpseo' );
			Arr::insert( $tabs, [ 'taxonomy-post_format' => $tab ], 5 );
		}

		return $tabs;
	}

	/**
	 * Check if certain fields got updated.
	 *
	 * @param int   $object_id The ID of the current object.
	 * @param array $updated   Array of field ids that were updated.
	 *                         Will only include field ids that had values change.
	 */
	public function check_updated_fields( $object_id, $updated ) {

		/**
		 * Filter: Allow developers to add option fields which will flush the rewrite rules when updated.
		 *
		 * @param array $flush_fields Array of field IDs for which we need to flush.
		 */
		$flush_fields = $this->do_filter(
			'flush_fields',
			[
				'cpseo_strip_category_base',
				'cpseo_disable_author_archives',
				'cpseo_url_author_base',
				'cpseo_attachment_redirect_urls',
				'cpseo_attachment_redirect_default',
				'cpseo_url_strip_stopwords',
				'cpseo_nofollow_external_links',
				'cpseo_nofollow_image_links',
				'cpseo_nofollow_domains',
				'cpseo_nofollow_exclude_domains',
				'cpseo_new_window_external_links',
				'cpseo_redirections_header_code',
				'cpseo_redirections_post_redirect',
				'cpseo_redirections_debug',
			]
		);

		foreach ( $flush_fields as $field_id ) {
			if ( in_array( $field_id, $updated, true ) ) {
				Helper::schedule_flush_rewrite();
				break;
			}
		}

	}
}
