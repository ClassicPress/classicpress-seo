<?php
/**
 * The AIO SEO Import Class
 *
 * @since      0.5.0
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin\Importers
 */

namespace Classic_SEO\Admin\Importers;

use Classic_SEO\Helper;
use Classic_SEO\Helpers\Str;
use Classic_SEO\Admin\Admin_Helper;
use Classic_SEO\Admin\Import_Export;

defined( 'ABSPATH' ) || exit;

/**
 * AIOSEO class.
 */
#[\AllowDynamicProperties]
class AIOSEO extends Plugin_Importer {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'All In One SEO Pack';

	/**
	 * Plugin options meta key.
	 *
	 * @var string
	 */
	protected $meta_key = '_aioseop_';

	/**
	 * Option keys to import and clean.
	 *
	 * @var array
	 */
	protected $option_keys = [ '_aioseop_%', 'aioseop_options' ];

	/**
	 * Choices keys to import.
	 *
	 * @var array
	 */
	protected $choices = [ 'settings', 'postmeta' ];

	/**
	 * Import settings of plugin.
	 *
	 * @return bool
	 */
	protected function settings() {
		$cpseo_backup = new Import_Export();
		$cpseo_backup->run_backup('add');

		$this->get_settings();
		$aioseo = get_option( 'aioseop_options' );

		// Titles & Descriptions.
		if ( ! empty( $aioseo['aiosp_home_title'] ) ) {
			$aioseo['aiosp_home_page_title_format'] = $aioseo['aiosp_home_title'];
		}
		$hash = [
			'aiosp_home_page_title_format' => 'cpseo_homepage_title',
			'aiosp_home_description'       => 'cpseo_homepage_description',
			'aiosp_author_title_format'    => 'cpseo_author_archive_title',
			'aiosp_date_title_format'      => 'cpseo_date_archive_title',
			'aiosp_search_title_format'    => 'cpseo_search_title',
		];
		$this->replace( $hash, $aioseo, $this->titles, 'convert_variables' );

		$this->titles['cpseo_title_separator'] = '|';	// Set default separator

		$this->post_types_settings();
		$this->taxonomies_settings();
		$this->opengraph_settings();
		$this->sitemap_settings();
		$this->update_settings();

		return true;
	}

	/**
	 * Post Types settings.
	 */
	private function post_types_settings() {

		$hash         = [];
		$aioseo       = get_option( 'aioseop_options' );
		$postnoindex  = isset( $aioseo['aiosp_cpostnoindex'] ) && is_array( $aioseo['aiosp_cpostnoindex'] ) ? $aioseo['aiosp_cpostnoindex'] : [];
		$postnofollow = isset( $aioseo['aiosp_cpostnofollow'] ) && is_array( $aioseo['aiosp_cpostnofollow'] ) ? $aioseo['aiosp_cpostnofollow'] : [];

		foreach ( Helper::get_accessible_post_types() as $post_type ) {
			$hash[ "aiosp_{$post_type}_title_format" ] = "cpseo_pt_{$post_type}_title";
			$this->set_robots_settings(
				in_array( $post_type, $postnoindex, true ),
				in_array( $post_type, $postnofollow, true ),
				$post_type
			);
		}

		$this->replace( $hash, $aioseo, $this->titles, 'convert_variables' );
	}

	/**
	 * Set global robots.
	 *
	 * @param bool   $noindex   Is noindex set.
	 * @param bool   $nofollow  Is nofollow set.
	 * @param string $post_type Current Post type.
	 */
	private function set_robots_settings( $noindex, $nofollow, $post_type ) {
		if ( ! $noindex && ! $nofollow ) {
			return;
		}

		$this->titles[ "cpseo_pt_{$post_type}_custom_robots" ] = 'on';
		if ( $noindex ) {
			$this->titles[ "cpseo_pt_{$post_type}_robots" ][] = 'noindex';
		}
		if ( $nofollow ) {
			$this->titles[ "cpseo_pt_{$post_type}_robots" ][] = 'nofollow';
		}
		$this->titles[ "cpseo_pt_{$post_type}_robots" ] = \array_unique( $this->titles[ "cpseo_pt_{$post_type}_robots" ] );
	}

	/**
	 * Taxonomies settings.
	 */
	private function taxonomies_settings() {
		$hash   = [];
		$aioseo = get_option( 'aioseop_options' );
		foreach ( Helper::get_accessible_taxonomies() as $taxonomy => $object ) {
			$convert = 'post_tag' === $taxonomy ? 'tag' : $taxonomy;
			$hash[ "aiosp_{$convert}_title_format" ] = "cpseo_tax_{$taxonomy}_title";

			$this->titles[ "cpseo_tax_{$taxonomy}_custom_robots" ] = 'on';
			$this->titles[ "cpseo_tax_{$taxonomy}_robots" ][]      = 'noindex';
			$this->titles[ "cpseo_tax_{$taxonomy}_robots" ]        = array_unique( $this->titles[ "cpseo_tax_{$taxonomy}_robots" ] );
		}

		$this->replace( $hash, $aioseo, $this->titles, 'convert_variables' );
	}

	/**
	 * Opengraph settings.
	 */
	private function opengraph_settings() {
		$aioseo = get_option( 'aioseop_options' );
		if ( empty( $aioseo['modules']['aiosp_opengraph_options'] ) || ! is_array( $aioseo['modules']['aiosp_opengraph_options'] ) ) {
			return;
		}

		$opengraph_settings = $aioseo['modules']['aiosp_opengraph_options'];
		$set_meta           = 'on' === $opengraph_settings['aiosp_opengraph_setmeta'];

		$this->titles['cpseo_homepage_facebook_title']       = $set_meta ? $this->titles['cpseo_homepage_title'] : $this->convert_variables( $opengraph_settings['aiosp_opengraph_hometitle'] );
		$this->titles['cpseo_homepage_facebook_description'] = $set_meta ? $this->titles['cpseo_homepage_description'] : $this->convert_variables( $opengraph_settings['aiosp_opengraph_description'] );

		if ( isset( $opengraph_settings['aiosp_opengraph_homeimage'] ) ) {
			$this->replace_image( $opengraph_settings['aiosp_opengraph_homeimage'], $this->titles, 'cpseo_homepage_facebook_image', 'cpseo_homepage_facebook_image_id' );
		}

		$this->titles['cpseo_facebook_admin_id'] = $opengraph_settings['aiosp_opengraph_key'];
		$this->titles['cpseo_facebook_app_id']   = $opengraph_settings['aiosp_opengraph_appid'];

		if ( isset( $opengraph_settings['aiosp_schema_site_represents'] ) && ! empty( $opengraph_settings['aiosp_schema_site_represents'] ) ) {
			Helper::update_modules( [ 'local-seo' => 'on' ] );

			if ( $opengraph_settings['aiosp_schema_site_represents'] == 'organization' ) {
				$this->titles['cpseo_knowledgegraph_name'] = $opengraph_settings['aiosp_schema_organization_name'];
				$this->titles['cpseo_knowledgegraph_type'] = 'company';
			}
			else {
				$this->titles['cpseo_knowledgegraph_name'] = $opengraph_settings['aiosp_schema_person_user'];
				$this->titles['cpseo_knowledgegraph_type'] = 'person';
			}

		}

		$this->social_links_settings( $opengraph_settings );
	}

	/**
	 * Social Links settings.
	 *
	 * @param array $settings Array of module settings.
	 */
	private function social_links_settings( $settings ) {
		if ( ! isset( $settings['aiosp_opengraph_profile_links'] ) || empty( $settings['aiosp_opengraph_profile_links'] ) ) {
			return;
		}

		$social_links = explode( "\n", $settings['aiosp_opengraph_profile_links'] );
		$social_links = array_filter( $social_links );
		if ( empty( $social_links ) ) {
			return;
		}

		foreach ( $social_links as $social_link ) {
			$this->convert_social_link( $social_link );
		}
	}

	/**
	 * Convert social link.
	 *
	 * @param string $link Link to check.
	 */
	private function convert_social_link( $link ) {
		$services = [ 'facebook', 'twitter' ];
		foreach ( $services as $service ) {
			if ( Str::contains( $service, $social_link ) ) {
				$this->titles[ 'cpseo_social_url_' . $service ] = $social_link;
				break;
			}
		}
	}

	/**
	 * Sitemap settings.
	 */
	private function sitemap_settings() {
		$aioseo = get_option( 'aioseop_options' );
		if ( empty( $aioseo['modules']['aiosp_sitemap_options'] ) || ! is_array( $aioseo['modules']['aiosp_sitemap_options'] ) ) {
			return;
		}

		$sitemap_settings = $aioseo['modules']['aiosp_sitemap_options'];

		// Sitemap.
		if ( isset( $sitemap_settings['aiosp_feature_manager_enable_sitemap'] ) && $sitemap_settings['aiosp_feature_manager_enable_sitemap'] == 'on' ) {
			Helper::update_modules( [ 'sitemap' => 'on' ] );
		}
		else {
			Helper::update_modules( [ 'sitemap' => 'off' ] );
		}
		$hash = [
			'aiosp_sitemap_max_posts'  => 'cpseo_items_per_page',
			'aiosp_sitemap_excl_pages' => 'cpseo_exclude_posts',
		];
		$this->replace( $hash, $sitemap_settings, $this->sitemap );

		// Sitemap - Exclude Terms.
		if ( ! empty( $sitemap_settings['aiosp_sitemap_excl_terms'] ) ) {
			$this->sitemap['cpseo_exclude_terms'] = implode( ',', $sitemap_settings['aiosp_sitemap_excl_terms'] );
		}

		// Sitemap - Author / User.
		$this->titles['cpseo_disable_author_archives'] = isset( $sitemap_settings['aiosp_sitemap_archive'] ) ? 'on' : 'off';

		$this->sitemap_post_types($sitemap_settings);
		$this->sitemap_taxonomies($sitemap_settings);
	}

	/**
	 * Sitemap - Post Types.
	 */
	private function sitemap_post_types($sitemap_settings) {
		$aioseo = get_option( 'aioseop_options' );
		$all    = in_array( 'all', $sitemap_settings['aiosp_sitemap_posttypes'], true );

		foreach ( Helper::get_accessible_post_types() as $post_type ) {
			$this->sitemap[ "cpseo_pt_{$post_type}_sitemap" ] = $all || in_array( $post_type, $sitemap_settings['aiosp_sitemap_posttypes'], true ) ? 'on' : 'off';
		}
	}

	/**
	 * Sitemap - Taxonomies.
	 */
	private function sitemap_taxonomies($sitemap_settings) {
		$aioseo = get_option( 'aioseop_options' );
		$all    = in_array( 'all', $sitemap_settings['aiosp_sitemap_taxonomies'], true );

		foreach ( Helper::get_accessible_taxonomies() as $taxonomy => $object ) {
			$this->sitemap[ "cpseo_tax_{$taxonomy}_sitemap" ] = $all || in_array( $taxonomy, $sitemap_settings['aiosp_sitemap_taxonomies'], true ) ? 'on' : 'off';
		}
	}

	/**
	 * Import post meta of plugin.
	 *
	 * @return array
	 */
	protected function postmeta() {
		$this->set_pagination( $this->get_post_ids( true ) );
		$post_ids = $this->get_post_ids();

		$hash = [
			'_aioseop_title'       => 'cpseo_title',
			'_aioseop_keywords'    => 'cpseo_focus_keyword',
			'_aioseop_description' => 'cpseo_description',
			'_aioseop_custom_link' => 'cpseo_canonical_url',
		];
		foreach ( $post_ids as $post ) {
			$post_id = $post->ID;
			$this->replace_meta( $hash, null, $post_id, 'post' );
			$this->set_post_robots( $post_id );

			$opengraph_meta = get_post_meta( $post_id, '_aioseop_opengraph_settings', true );
			if ( ! empty( $opengraph_meta ) && is_array( $opengraph_meta ) ) {
				$this->set_post_opengraph( $post_id );
			}
		}

		return $this->get_pagination_arg();
	}

	/**
	 * Set OpenGraph.
	 *
	 * @param int $post_id Post ID.
	 */
	private function set_post_opengraph( $post_id ) {
		$opengraph_meta = get_post_meta( $post_id, '_aioseop_opengraph_settings', true );

		if ( ! empty( $opengraph_meta['aioseop_opengraph_settings_title'] ) ) {
			update_post_meta( $post_id, 'cpseo_facebook_title', $opengraph_meta['aioseop_opengraph_settings_title'] );
			update_post_meta( $post_id, 'cpseo_twitter_title', $opengraph_meta['aioseop_opengraph_settings_title'] );
		}

		if ( ! empty( $opengraph_meta['aioseop_opengraph_settings_desc'] ) ) {
			update_post_meta( $post_id, 'cpseo_facebook_description', $opengraph_meta['aioseop_opengraph_settings_desc'] );
			update_post_meta( $post_id, 'cpseo_twitter_description', $opengraph_meta['aioseop_opengraph_settings_desc'] );
		}

		$og_thumb = ! empty( $opengraph_meta['aioseop_opengraph_settings_customimg'] ) ? $opengraph_meta['aioseop_opengraph_settings_customimg'] : ( ! empty( $opengraph_meta['aioseop_opengraph_settings_image'] ) ? $opengraph_meta['aioseop_opengraph_settings_image'] : '' );
		if ( ! empty( $og_thumb ) ) {
			$this->replace_image( $og_thumb, 'post', 'cpseo_facebook_image', 'cpseo_facebook_image_id', $post_id );
		}

		if ( ! empty( $opengraph_meta['aioseop_opengraph_settings_setcard'] ) ) {
			$twitter_card_type = 'summary' === $opengraph_meta['aioseop_opengraph_settings_setcard'] ? 'summary_card' : 'summary_large_image';
			update_post_meta( $post_id, 'cpseo_twitter_card_type', $twitter_card_type );
		}
	}

	/**
	 * Set post robots meta.
	 *
	 * @param int $post_id Post ID.
	 */
	private function set_post_robots( $post_id ) {
		// Early bail if robots data is set in Classic SEO plugin.
		if ( ! empty( $this->get_meta( 'post', $post_id, 'cpseo_robots' ) ) ) {
			return;
		}

		// ROBOTS.
		$robots   = [];
		$aioseo   = get_option( 'aioseop_options' );
		$robots[] = $this->get_noindex_robot( $post_id, $aioseo );
		$robots[] = $this->get_follow_robot( $post_id, $aioseo );

		update_post_meta( $post_id, 'cpseo_robots', array_unique( $robots ) );
	}

	/**
	 * Get noindex robot.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $aioseo  Option array.
	 *
	 * @return string
	 */
	private function get_noindex_robot( $post_id, $aioseo ) {
		$noindex         = get_post_meta( $post_id, '_aioseop_noindex', true );
		$exclude_sitemap = $this->is_post_excluded_sitemap( $post_id );

		if ( empty( $noindex ) ) {
			return in_array( get_post_type( $post_id ), $aioseo['aiosp_cpostnoindex'], true ) || $exclude_sitemap ? 'noindex' : 'index';
		}

		return 'on' === $noindex || $exclude_sitemap ? 'noindex' : 'index';
	}

	/**
	 * Get follow robot.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $aioseo  Option array.
	 *
	 * @return string
	 */
	private function get_follow_robot( $post_id, $aioseo ) {
		$nofollow = get_post_meta( $post_id, '_aioseop_nofollow', true );

		if ( empty( $nofollow ) ) {
			return in_array( get_post_type( $post_id ), $aioseo['aiosp_cpostnofollow'], true ) ? 'nofollow' : '';
		}

		return 'on' === $nofollow ? 'nofollow' : '';
	}

	/**
	 * Is post excluded from sitemap.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return bool
	 */
	private function is_post_excluded_sitemap( $post_id ) {
		$exclude_sitemap = get_post_meta( $post_id, '_aioseop_sitemap_exclude', true );
		return 'on' === $exclude_sitemap ? true : false;
	}

	/**
	 * Get the actions which can be performed for the plugin.
	 *
	 * @return array
	 */
	public function get_choices() {
		return [
			'settings' => esc_html__( 'Import Settings', 'cpseo' ) . Admin_Helper::get_tooltip( esc_html__( 'Import AIO SEO plugin settings, global meta, sitemap settings, etc.', 'cpseo' ) ),
			'postmeta' => esc_html__( 'Import Post Meta', 'cpseo' ) . Admin_Helper::get_tooltip( esc_html__( 'Import meta information of your posts/pages like the titles, descriptions, robots meta, OpenGraph info, etc.', 'cpseo' ) ),
		];
	}

	/**
	 * Convert Yoast / AIO SEO variables if needed.
	 *
	 * @param string $string Value to convert.
	 * @return string
	 */
	public function convert_variables( $string ) {
		$string = str_replace( '%blog_title%', '%sitename%', $string );
		$string = str_replace( '%site_title%', '%sitename%', $string );
		$string = str_replace( '%blog_description%', '%sitedesc%', $string );
		$string = str_replace( '%site_description%', '%sitedesc%', $string );
		$string = str_replace( '%post_title%', '%title%', $string );
		$string = str_replace( '%page_title%', '%title%', $string );
		$string = str_replace( '%category%', '%category%', $string );
		$string = str_replace( '%category_title%', '%category%', $string );
		$string = str_replace( '%category_description%', '%term_description%', $string );
		$string = str_replace( '%archive_title%', '%term%', $string );
		$string = str_replace( '%current_date%', '%currentdate%', $string );
		$string = str_replace( '%post_date%', '%date%', $string );
		$string = str_replace( '%post_year%', '%date(Y)%', $string );
		$string = str_replace( '%post_month%', '%date(M)%', $string );
		$string = str_replace( '%page_author_login%', '%name%', $string );
		$string = str_replace( '%page_author_nicename%', '%name%', $string );
		$string = str_replace( '%page_author_firstname%', '%name%', $string );
		$string = str_replace( '%page_author_lastname%', '%name%', $string );
		$string = str_replace( '%author%', '%name%', $string );
		$string = str_replace( '%search%', '%search_query%', $string );

		return str_replace( '%%', '%', $string );
	}
}
