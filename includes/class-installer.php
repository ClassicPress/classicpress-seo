<?php
/**
 * Plugin activation and deactivation functionality.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Core

 */

namespace Classic_SEO;

use Classic_SEO\Traits\Hooker;
use Classic_SEO\Admin\Watcher;
use Classic_SEO\Helpers\WordPress;
use Classic_SEO\Role_Manager\Capability_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Installer class.
 */
class Installer {

	use Hooker, WordPress;

	/**
	 * Bind all events.
	 */
	public function __construct() {
		register_activation_hook( CPSEO_FILE, [ $this, 'activation' ] );
		register_deactivation_hook( CPSEO_FILE, [ $this, 'deactivation' ] );

		$this->action( 'wp', 'create_cron_jobs' );
		$this->action( 'wpmu_new_blog', 'activate_blog' );
		$this->action( 'activate_blog', 'activate_blog' );
		$this->filter( 'wpmu_drop_tables', 'on_delete_blog' );
	}

	/**
	 * Do stuff when activating Classic SEO.
	 *
	 * @param bool $network_wide Whether the plugin is being activated network-wide.
	 */
	public function activation( $network_wide = false ) {
		if ( ! is_multisite() || ! $network_wide ) {
			$this->activate();
			return;
		}

		$this->network_activate_deactivate( true );
	}

	/**
	 * Do stuff when deactivating Classic SEO.
	 *
	 * @param bool $network_wide Whether the plugin is being activated network-wide.
	 */
	public function deactivation( $network_wide = false ) {
		if ( ! is_multisite() || ! $network_wide ) {
			$this->deactivate();
			return;
		}

		$this->network_activate_deactivate( false );
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @param int $blog_id ID of the new blog.
	 */
	public function activate_blog( $blog_id ) {
		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		$this->activate();
		restore_current_blog();
	}

	/**
	 * Uninstall tables when MU blog is deleted.
	 *
	 * @param  array $tables List of tables that will be deleted by CP.
	 * @return array
	 */
	public function on_delete_blog( $tables ) {
		global $wpdb;

		$tables[] = $wpdb->prefix . 'cpseo_404_logs';
		$tables[] = $wpdb->prefix . 'cpseo_redirections';
		$tables[] = $wpdb->prefix . 'cpseo_redirections_cache';
		$tables[] = $wpdb->prefix . 'cpseo_internal_links';
		$tables[] = $wpdb->prefix . 'cpseo_internal_meta';

		return $tables;
	}

	/**
	 * Run network-wide activation/deactivation of the plugin.
	 *
	 * @param bool $activate True for plugin activation, false for de-activation.
	 */
	private function network_activate_deactivate( $activate ) {
		global $wpdb;

		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs WHERE archived = '0' AND spam = '0' AND deleted = '0'" );
		if ( empty( $blog_ids ) ) {
			return;
		}

		foreach ( $blog_ids as $blog_id ) {
			$func = true === $activate ? 'activate' : 'deactivate';

			switch_to_blog( $blog_id );
			$this->$func();
			restore_current_blog();
		}
	}

	/**
	 * Runs on activation of the plugin.
	 */
	private function activate() {
		$current_version		= get_option( 'cpseo_version', null );
		$current_db_version		= get_option( 'cpseo_db_version', null );

		$this->create_tables();
		$this->create_options();
		$this->set_capabilities();
		$this->create_cron_jobs();

		if ( is_null( $current_version ) && is_null( $current_db_version ) ) {
			set_transient( '_cpseo_activation_redirect', 1, 30 );
		}

		// Update to latest version.
		update_option( 'cpseo_version', CPSEO_VERSION );
		update_option( 'cpseo_db_version', CPSEO_DB_VERSION );

		// Save install date.
		if ( false === boolval( get_option( 'cpseo_install_date' ) ) ) {
			update_option( 'cpseo_install_date', current_time( 'timestamp' ) );
		}

		// Activate Watcher.
		$watcher = new Watcher;
		$watcher->check_activated_plugin();

		$this->clear_rewrite_rules( true );
		Helper::clear_cache();
		$this->do_action( 'activate' );
	}

	/**
	 * Runs on deactivation of the plugin.
	 */
	private function deactivate() {
		$this->clear_rewrite_rules( false );
		$this->remove_cron_jobs();
		Helper::clear_cache();
		$this->do_action( 'deactivate' );
	}

	/**
	 * Set up the database tables.
	 */
	private function create_tables() {
		global $wpdb;

		$collate      = $wpdb->get_charset_collate();
		$table_schema = [

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cpseo_404_logs (
				id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
				uri VARCHAR(255) NOT NULL,
				accessed DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
				times_accessed BIGINT(20) unsigned NOT NULL DEFAULT 1,
				ip VARCHAR(50) NOT NULL DEFAULT '',
				referer VARCHAR(255) NOT NULL DEFAULT '',
				user_agent VARCHAR(255) NOT NULL DEFAULT '',
				PRIMARY KEY (id),
				KEY uri (uri(191))
			) $collate;",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cpseo_redirections (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				sources TEXT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->charset}_bin NOT NULL,
				url_to TEXT NOT NULL,
				header_code SMALLINT(4) UNSIGNED NOT NULL,
				hits BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
				status VARCHAR(25) NOT NULL DEFAULT 'active',
				created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
				updated DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
				last_accessed DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
				PRIMARY KEY (id),
				KEY (status)
			) $collate;",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cpseo_redirections_cache (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				from_url TEXT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->charset}_bin NOT NULL,
				redirection_id BIGINT(20) UNSIGNED NOT NULL,
				object_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
				object_type VARCHAR(10) NOT NULL DEFAULT 'post',
				is_redirected TINYINT(1) NOT NULL DEFAULT '0',
				PRIMARY KEY (id),
				KEY (redirection_id)
			) $collate;",

			// Link Storage.
			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cpseo_internal_links (
				id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
				url VARCHAR(255) NOT NULL,
				post_id bigint(20) unsigned NOT NULL,
				target_post_id bigint(20) unsigned NOT NULL,
				type VARCHAR(8) NOT NULL,
				PRIMARY KEY (id),
				KEY link_direction (post_id, type)
			) $collate;",

			// Link meta.
			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cpseo_internal_meta (
				object_id bigint(20) UNSIGNED NOT NULL,
				internal_link_count int(10) UNSIGNED NULL DEFAULT 0,
				external_link_count int(10) UNSIGNED NULL DEFAULT 0,
				incoming_link_count int(10) UNSIGNED NULL DEFAULT 0,
				UNIQUE KEY object_id (object_id)
			) $collate;",

		];

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		foreach ( $table_schema as $table ) {
			dbDelta( $table );
		}
	}

	/**
	 * Create options.
	 */
	private function create_options() {
		$this->create_misc_options();
		$this->create_general_options();
		$this->create_titles_sitemaps_options();
	}

	/**
	 * Create misc options.
	 */
	private function create_misc_options() {
		// Update "known CPTs" list, so we can send notice about new ones later.
		add_option( 'cpseo_known_post_types', Helper::get_accessible_post_types() );

		$modules = [
			'link-counter',
			'seo-analysis',
			'sitemap',
			'rich-snippet',
			'woocommerce',
			'acf',
		];

		// Role Manager.
		$users = get_users( [ 'role__in' => [ 'administrator', 'editor', 'author', 'contributor' ] ] );
		if ( count( $users ) > 1 ) {
			$modules[] = 'role-manager';
		}

		// If AMP plugin is installed.
		if ( function_exists( 'is_amp_endpoint' ) || class_exists( 'Better_AMP' ) || class_exists( 'Weeblramp_Api' ) || class_exists( 'AMPHTML' ) ) {
			$modules[] = 'amp';
		}

		// If 404-monitor is active as plugin.
		if ( false !== get_option( 'cpseo_monitor_version', false ) ) {
			$modules[] = '404-monitor';
		}

		add_option( 'cpseo_modules', $modules );
	}

	/**
	 * Add defaults for general options.
	 */
	private function create_general_options() {
		add_option( 'cpseo-options-general', $this->do_filter( 'settings/defaults/general', [
			'cpseo_strip_category_base'                 => 'off',
			'cpseo_attachment_redirect_urls'            => 'on',
			'cpseo_attachment_redirect_default'         => get_home_url(),
			'cpseo_url_strip_stopwords'                 => 'off',
			'cpseo_nofollow_external_links'             => 'off',
			'cpseo_nofollow_image_links'                => 'on',
			'cpseo_new_window_external_links'           => 'on',
			'cpseo_add_img_alt'                         => 'off',
			'cpseo_img_alt_format'                      => '%title% %count(alt)%',
			'cpseo_add_img_title'                       => 'off',
			'cpseo_img_title_format'                    => '%title% %count(title)%',
			'cpseo_breadcrumbs'                         => 'off',
			'cpseo_breadcrumbs_separator'               => '-',
			'cpseo_breadcrumbs_home'                    => 'on',
			'cpseo_breadcrumbs_home_label'              => esc_html__( 'Home', 'cpseo' ),
			/* translators: Archive title */
			'cpseo_breadcrumbs_archive_format'          => esc_html__( 'Archives for %s', 'cpseo' ),
			/* translators: Search query term */
			'cpseo_breadcrumbs_search_format'           => esc_html__( 'Results for %s', 'cpseo' ),
			'cpseo_breadcrumbs_404_label'               => esc_html__( '404 Error: page not found', 'cpseo' ),
			'cpseo_breadcrumbs_ancestor_categories'     => 'off',
			'cpseo_breadcrumbs_blog_page'               => 'off',
			'cpseo_404_monitor_mode'                    => 'simple',
			'cpseo_404_monitor_limit'                   => 100,
			'cpseo_404_monitor_ignore_query_parameters' => 'on',
			'cpseo_redirections_header_code'            => '301',
			'cpseo_redirections_debug'                  => 'off',
			'cpseo_link_builder_links_per_page'         => '7',
			'cpseo_link_builder_links_per_target'       => '1',
			'cpseo_wc_remove_product_base'              => 'off',
			'cpseo_wc_remove_category_base'             => 'off',
			'cpseo_wc_remove_category_parent_slugs'     => 'off',
			'cpseo_rss_before_content'                  => '',
			'cpseo_rss_after_content'                   => '',
			'cpseo_wc_remove_generator'                 => 'on',
			'cpseo_remove_shop_snippet_data'            => 'on',
			'cpseo_usage_tracking'                      => 'on',
		]));
	}

	/**
	 * Add default values.
	 */
	private function create_titles_sitemaps_options() {
		$sitemap = [
			'cpseo_items_per_page'         => 200,
			'cpseo_include_images'         => 'on',
			'cpseo_include_featured_image' => 'off',
			'cpseo_ping_search_engines'    => 'on',
			'cpseo_exclude_roles'          => $this->get_excluded_roles(),
		];
		$titles  = [
			'cpseo_metabox_priority'           => 'default',
			'cpseo_noindex_empty_taxonomies'   => 'on',
			'cpseo_title_separator'            => '|',
			'cpseo_capitalize_titles'          => 'off',
			'cpseo_twitter_card_type'          => 'summary_large_image',
			'cpseo_knowledgegraph_type'        => class_exists( 'Easy_Digital_Downloads' ) || class_exists( 'WooCommerce' ) ? 'company' : 'person',
			'cpseo_knowledgegraph_name'        => get_bloginfo( 'name' ),
			'cpseo_local_business_type'        => 'Organization',
			'cpseo_local_address_format'       => '{address} {locality}, {region} {postalcode}',
			'cpseo_opening_hours'              => $this->get_opening_hours(),
			'cpseo_opening_hours_format'       => 'off',
			'cpseo_homepage_title'             => '%sitename% %page% %sep% %sitedesc%',
			'cpseo_homepage_description'       => '',
			'cpseo_homepage_custom_robots'     => 'off',
			'cpseo_disable_author_archives'    => 'off',
			'cpseo_url_author_base'            => 'author',
			'cpseo_author_custom_robots'       => 'on',
			'cpseo_author_robots'              => [ 'noindex' ],
			'cpseo_author_archive_title'       => '%name% %sep% %sitename% %page%',
			'cpseo_author_add_meta_box'        => 'on',
			'cpseo_disable_date_archives'      => 'off',
			'cpseo_date_archive_title'         => '%date% %page% %sep% %sitename%',
			'cpseo_search_title'               => '%search_query% %page% %sep% %sitename%',
			'cpseo_404_title'                  => 'Page Not Found %sep% %sitename%',
			'cpseo_date_archive_robots'        => [ 'noindex' ],
			'cpseo_noindex_search'             => 'on',
			'cpseo_noindex_archive_subpages'   => 'off',
			'cpseo_noindex_password_protected' => 'off',
		];

		$this->create_post_type_options( $titles, $sitemap );
		$this->create_taxonomy_options( $titles, $sitemap );

		add_option( 'cpseo-options-titles', $this->do_filter( 'settings/defaults/titles', $titles ) );
		add_option( 'cpseo-options-sitemap', $this->do_filter( 'settings/defaults/sitemap', $sitemap ) );
	}

	/**
	 * Create post type options.
	 *
	 * @param array $titles  Hold title settings.
	 * @param array $sitemap Hold sitemap settings.
	 */
	private function create_post_type_options( &$titles, &$sitemap ) {
		$post_types   = Helper::get_accessible_post_types();
		$post_types[] = 'product';

		foreach ( $post_types as $post_type ) {
			$defaults = $this->get_post_type_defaults( $post_type );

			$titles[ 'cpseo_pt_' . $post_type . '_title' ]                = '%title% %sep% %sitename%';
			$titles[ 'cpseo_pt_' . $post_type . '_description' ]          = '%excerpt%';
			$titles[ 'cpseo_pt_' . $post_type . '_robots' ]               = $defaults['robots'];
			$titles[ 'cpseo_pt_' . $post_type . '_custom_robots' ]        = $defaults['is_custom'];
			$titles[ 'cpseo_pt_' . $post_type . '_default_rich_snippet' ] = $defaults['rich_snippet'];
			$titles[ 'cpseo_pt_' . $post_type . '_default_article_type' ] = $defaults['article_type'];
			$titles[ 'cpseo_pt_' . $post_type . '_default_snippet_name' ] = '%seo_title%';
			$titles[ 'cpseo_pt_' . $post_type . '_default_snippet_desc' ] = '%seo_description%';

			if ( $this->has_archive( $post_type ) ) {
				$titles[ 'cpseo_pt_' . $post_type . '_archive_title' ] = '%title% %page% %sep% %sitename%';
			}

			if ( 'attachment' === $post_type ) {
				$sitemap[ 'cpseo_pt_' . $post_type . '_sitemap' ]     = 'off';
				$titles[ 'cpseo_pt_' . $post_type . '_add_meta_box' ] = 'off';
				continue;
			}

			$sitemap[ 'cpseo_pt_' . $post_type . '_sitemap' ]         = 'on';
			$titles[ 'cpseo_pt_' . $post_type . '_ls_use_fk' ]        = 'titles';
			$titles[ 'cpseo_pt_' . $post_type . '_add_meta_box' ]     = 'on';
			$titles[ 'cpseo_pt_' . $post_type . '_bulk_editing' ]     = 'editing';
			$titles[ 'cpseo_pt_' . $post_type . '_link_suggestions' ] = 'on';

			// Primary Taxonomy.
			$taxonomy_hash = [
				'post'    => 'category',
				'product' => 'product_cat',
			];

			if ( isset( $taxonomy_hash[ $post_type ] ) ) {
				$titles[ 'cpseo_pt_' . $post_type . '_primary_taxonomy' ] = $taxonomy_hash[ $post_type ];
			}
		}
	}

	/**
	 * Get robots default for post type.
	 *
	 * @param  string $post_type Post type.
	 * @return array
	 */
	private function get_post_type_defaults( $post_type ) {
		$rich_snippets = [
			'post'    => 'article',
			'page'    => 'article',
			'product' => 'product',
		];

		$defaults = [
			'robots'       => [],
			'is_custom'    => 'off',
			'rich_snippet' => isset( $rich_snippets[ $post_type ] ) ? $rich_snippets[ $post_type ] : 'off',
			'article_type' => 'post' === $post_type ? 'BlogPosting' : 'Article',
		];

		if ( 'attachment' === $post_type ) {
			$defaults['is_custom'] = 'on';
			$defaults['robots']    = [ 'noindex' ];
		}

		return $defaults;
	}

	/**
	 * Check post type has archive.
	 *
	 * @param  string $post_type Post type.
	 * @return bool
	 */
	private function has_archive( $post_type ) {
		$post_type_obj = get_post_type_object( $post_type );
		return ! is_null( $post_type_obj ) && $post_type_obj->has_archive;
	}

	/**
	 * Create post type options.
	 *
	 * @param array $titles  Hold title settings.
	 * @param array $sitemap Hold sitemap settings.
	 */
	private function create_taxonomy_options( &$titles, &$sitemap ) {
		$taxonomies = Helper::get_accessible_taxonomies();
		foreach ( $taxonomies as $taxonomy => $object ) {
			$defaults = $this->get_taxonomy_defaults( $taxonomy );

			$titles[ 'cpseo_tax_' . $taxonomy . '_title' ]         = '%term% %sep% %sitename%';
			$titles[ 'cpseo_tax_' . $taxonomy . '_robots' ]        = $defaults['robots'];
			$titles[ 'cpseo_tax_' . $taxonomy . '_add_meta_box' ]  = $defaults['metabox'];
			$titles[ 'cpseo_tax_' . $taxonomy . '_custom_robots' ] = $defaults['is_custom'];

			$sitemap[ 'cpseo_tax_' . $taxonomy . '_sitemap' ] = 'category' === $taxonomy ? 'on' : 'off';
		}
	}

	/**
	 * Get robots default for post type.
	 *
	 * @param  string $taxonomy Taxonomy.
	 * @return array
	 */
	private function get_taxonomy_defaults( $taxonomy ) {
		$defaults = [
			'robots'    => [],
			'is_custom' => 'off',
			'metabox'   => 'category' === $taxonomy ? 'on' : 'off',
		];

		if ( in_array( $taxonomy, [ 'post_tag', 'post_format', 'product_tag' ], true ) ) {
			$defaults['is_custom'] = 'on';
			$defaults['robots']    = [ 'noindex' ];
		}

		return $defaults;
	}

	/**
	 * Create capabilities.
	 */
	private function set_capabilities() {
		$admin = get_role( 'administrator' );

		Capability_Manager::get()->create_capabilities();
	}

	/**
	 * Create cron jobs.
	 */
	public function create_cron_jobs() {
		$midnight = strtotime( 'tomorrow midnight' );
		foreach ( $this->get_cron_jobs() as $job => $recurrence ) {
			if ( ! wp_next_scheduled( "cpseo/{$job}" ) ) {
				wp_schedule_event( $midnight, $this->do_filter( "{$job}_recurrence", $recurrence ), "cpseo/{$job}" );
			}
		}
	}

	/**
	 * Remove cron jobs.
	 */
	private function remove_cron_jobs() {
		foreach ( $this->get_cron_jobs() as $job => $recurrence ) {
			wp_clear_scheduled_hook( "cpseo/{$job}" );
		}
	}

	/**
	 * Get cron jobs.
	 *
	 * @return array
	 */
	private function get_cron_jobs() {
		return [
			'redirection/clean_trashed'    => 'daily',  // Add cron for cleaning trashed redirects.
			'links/internal_links'         => 'daily',  // Add cron for counting links.
		];
	}

	/**
	 * Get opening hours.
	 *
	 * @return array
	 */
	private function get_opening_hours() {
		$hours = [];
		$days  = [ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ];
		foreach ( $days as $day ) {
			$hours[] = [
				'day'  => $day,
				'time' => '09:00-17:00',
			];
		}

		return $hours;
	}

	/**
	 * Get roles to exclude.
	 *
	 * @return array
	 */
	private function get_excluded_roles() {
		$roles = Installer::get_roles();
		unset( $roles['administrator'], $roles['editor'], $roles['author'] );

		return $roles;
	}

	/**
	 * Clear rewrite rules.
	 *
	 * @param bool $activate True for plugin activation, false for de-activation.
	 */
	private function clear_rewrite_rules( $activate ) {
		if ( is_multisite() && ms_is_switched() ) {
			delete_option( 'rewrite_rules' );
			Helper::schedule_flush_rewrite();
			return;
		}

		// On activation.
		if ( $activate ) {
			Helper::schedule_flush_rewrite();
			return;
		}

		// On deactivation.
		add_action( 'shutdown', 'flush_rewrite_rules' );
	}
}
