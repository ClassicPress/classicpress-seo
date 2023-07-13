<?php
/**
 * The SEO Framework Import Class
 *
 * @since      0.7.0
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
 * SEO_Framework class.
 */
#[\AllowDynamicProperties]
class SEO_Framework extends Plugin_Importer {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'The SEO Framework';

	/**
	 * Plugin options meta_key in postmeta table
	 *
	 * @var string
	 */
	protected $meta_key = '_genesis_';

	/**
	 * The option_name in the options table. Option keys to import and clean.
	 *
	 * @var array
	 */
	protected $option_keys = [ 'autodescription-site-settings' ];

	/**
	 * Choices keys to import.
	 * Possible options are: settings, postmeta, termmeta, usermeta, redirections
	 *
	 * @var array
	 */
	protected $choices = [ 'settings', 'postmeta' ];

	/**
	 * Array of robots.txt tags
	 */
	protected $robotags	= [ 'noindex', 'nofollow', 'noarchive' ];

	/**
	 * Array for all SEO Framework settings
	 */
	protected $seoframework = [];

	/**
	 * Import settings of plugin.
	 *
	 * @return bool
	 */
	protected function settings() {
		$cpseo_backup = new Import_Export();
		$cpseo_backup->run_backup('add');

		$this->get_settings();
		$this->seoframework = get_option( 'autodescription-site-settings' );

		$hash = [
			'homepage_title'			=> 'cpseo_homepage_title',
			'homepage_description'		=> 'cpseo_homepage_description',
			'homepage_og_title'			=> 'cpseo_homepage_facebook_title',
			'homepage_og_description'	=> 'cpseo_homepage_facebook_description',
			'homepage_social_image_url'	=> 'cpseo_homepage_facebook_image',
			'homepage_social_image_id'	=> 'cpseo_homepage_facebook_image_id',
			'social_image_fb_url'		=> 'cpseo_open_graph_image',
			'social_image_fb_id'		=> 'cpseo_open_graph_image_id',
			'facebook_appid'			=> 'cpseo_facebook_app_id',
			'facebook_publisher'		=> 'cpseo_facebook_author_urls',
			'twitter_card'				=> 'cpseo_twitter_card_type',
			'twitter_creator'			=> 'cpseo_twitter_author_names',
			'ld_json_breadcrumbs'		=> 'cpseo_breadcrumbs',
			'knowledge_logo_url'		=> 'cpseo_knowledgegraph_logo',
			'knowledge_logo_id'			=> 'cpseo_knowledgegraph_logo_id',
			'knowledge_facebook'		=> 'cpseo_social_url_facebook',
			'knowledge_twitter'			=> 'cpseo_social_url_twitter',
			'knowledge_instagram'		=> 'cpseo_social_url_instagram',
			'knowledge_youtube'			=> 'cpseo_social_url_youtube',
			'knowledge_linkedin'		=> 'cpseo_social_url_linkedin',
			'knowledge_pinterest'		=> 'social_url_pinterest',
			'paged_noindex'				=> 'cpseo_noindex_paginated_pages',
			'search_noindex'			=> 'cpseo_noindex_search',
			'site_noindex'				=> 'blog_public',
			'author_noindex'			=> 'cpseo_disable_author_archives',
			'date_noindex'				=> 'cpseo_disable_date_archives',
		];

		$this->settings['cpseo_disable_author_archives']	= isset( $this->seoframework['author_noindex'] ) ? 'on' : 'off';
		$this->settings['cpseo_disable_date_archives']		= isset( $this->seoframework['date_noindex'] ) ? 'on' : 'off';

		/*
		 * $hash = array of hash for search and replace.
		 * $this->seoframework = array for source where to search.
		 * $this->settings = array for destination where to save.
		 */
		$this->replace( $hash, $this->seoframework, $this->settings, 'convert_bool' );

		$this->set_post_types();
		$this->set_separator();
		$this->taxonomies_settings();
		$this->opengraph_settings();
		$this->sitemap_settings();
		$this->update_settings();

		return true;
	}


	/**
	 * Set post type settings.
	 *
	 * @param array $this->seoframework Settings.
	 */
	private function set_post_types() {
		$hash = [];
		$cp_post_types	= Helper::get_accessible_post_types();

		// Post types noindex etc.
		foreach($cp_post_types as $post_type) {
			$this->titles[ "cpseo_pt_{$post_type}_robots" ] = [];
			foreach ($this->robotags as $robotag) {
				$hash[ "cpseo_pt_{$post_type}_custom_robots" ] = 'on';
				$this->titles[ "cpseo_pt_{$post_type}_robots" ][] = $this->seoframework[ "{$robotag}_post_types" ][ $post_type ] == 1 ? $robotag : '';
			}
		}

		$this->replace( $hash, $this->seoframework, $this->titles );
	}


	/**
	 * Import post meta of plugin.
	 *
	 * @return array
	 */
	protected function postmeta() {
		$this->set_pagination( $this->get_post_ids( true ) );
		$post_ids = $this->get_post_ids();
		$hash = [];

		$hash = [
			'_genesis_title'			=> 'cpseo_title',
			'_genesis_description'		=> 'cpseo_description',
			'_genesis_canonical_uri'	=> 'cpseo_canonical_url',
			'redirect'					=> 'cpseo_redirection_url_to',
		];

		foreach ( $post_ids as $post ) {
			$post_id = $post->ID;
			$this->replace_meta( $hash, null, $post_id, 'post' );
			$this->set_post_robots( $post_id );
			$this->set_post_opengraph( $post_id );
		}

		return $this->get_pagination_arg();
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
		$robots   = [];
		$robots[] = 'on' === get_post_meta( $post_id, 'noindex', true ) ? 'noindex' : 'index';
		$robots[] = 'on' === get_post_meta( $post_id, 'nofollow', true ) ? 'nofollow' : '';
		$robots[] = 'on' === get_post_meta( $post_id, 'nofollow', true ) ? 'nofollow' : '';

		update_post_meta( $post_id, 'cpseo_robots', array_filter( array_unique( $robots ) ) );
	}


	/**
	 * Taxonomies settings (e.g. category, post_type).
	 */
	private function taxonomies_settings() {
		$cp_tax_types	= Helper::get_accessible_taxonomies();

		foreach($cp_tax_types as $tax_type) {
			foreach ($this->robotags as $robotag) {
				$tsf_taxname = $tax_type->name == 'post_tag' ? 'tag' : $tax_type->name;
				if ( isset( $this->seoframework[ "{$tsf_taxname}_{$robotag}" ] ) ) {
					$cpseo_opts[ "cpseo_tax_{$tax_type->name}_custom_robots" ] = 'on';
					$cpseo_opts[ "cpseo_tax_{$tax_type->name}_robots" ][ $robotag ] = $robotag;
				}
			}
		}

		$this->replace( $hash, $this->seoframework, $this->titles );
	}


	/**
	 * Set separator.
	 */
	private function set_separator() {
		if ( ! isset( $this->seoframework['title_separator'] ) ) {
			return;
		}

		$separator_options = [
			'dash'   => '-',
			'ndash'  => '&ndash;',
			'mdash'  => '&mdash;',
			'raquo'  => '&raquo;',
			'pipe'   => '|',
			'bull'   => '&bull;',
		];

		if ( isset( $separator_options[ $this->seoframework['title_separator'] ] ) ) {
			$this->titles['cpseo_title_separator'] = $separator_options[ $this->seoframework['title_separator'] ];
		}
	}


	/**
	 * Opengraph settings.
	 */
	private function opengraph_settings() {
		$hash = [];

		if ( isset( $this->seoframework['knowledge_output'] ) && $this->seoframework['knowledge_output'] == '1' ) {
			Helper::update_modules( [ 'local-seo' => 'on' ] );
		}
		else {
			Helper::update_modules( [ 'local-seo' => 'off' ] );
			return;
		}

		$hash = [
			'homepage_og_title'			=> 'cpseo_homepage_facebook_title',
			'homepage_og_description'	=> 'cpseo_homepage_facebook_description',
			'facebook_appid'			=> 'cpseo_facebook_app_id',
			'knowledge_name'			=> 'cpseo_knowledgegraph_name',
			'knowledge_type'			=> 'cpseo_knowledgegraph_type',
		];

		if ( isset( $this->seoframework['homepage_og_description'] ) ) {
			$this->replace_image( $this->seoframework['homepage_og_description'], $this->settings, 'cpseo_homepage_facebook_image', 'cpseo_homepage_facebook_image_id' );
		}

		if ( isset( $this->seoframework['knowledge_type'] ) && ! empty( $this->seoframework['knowledge_type'] ) ) {
			if ( $this->seoframework['knowledge_type'] == 'organization' ) {
				$this->settings['cpseo_knowledgegraph_type'] = 'company';
			}
			else {
				$this->settings['cpseo_knowledgegraph_type'] = 'person';
			}
		}
		$this->replace( $hash, $this->seoframework, $this->settings );
	}


	/**
	 * Sitemap settings.
	 */
	private function sitemap_settings() {
		$hash = [];
		$cp_tax_types	= Helper::get_accessible_taxonomies();
		$cp_post_types	= Helper::get_accessible_post_types();

		if ( isset( $this->seoframework['sitemaps_output'] ) && $this->seoframework['sitemaps_output'] == '1' ) {
			Helper::update_modules( [ 'sitemap' => 'on' ] );
		}
		else {
			Helper::update_modules( [ 'sitemap' => 'off' ] );
			return;
		}

		// Post types noindex etc.
		foreach($cp_post_types as $post_type) {
			$this->sitemap[ "cpseo_pt_{$post_type}_sitemap" ] = $this->seoframework[ "{$post_type}_sitemap" ][ $post_type ] == 1 ? "on" : "off";
		}

		foreach($cp_tax_types as $tax_type) {
			foreach ($this->robotags as $robotag) {
				$tsf_taxname = $tax_type->name == 'post_tag' ? 'tag' : $tax_type->name;
				$this->sitemap[ "cpseo_tax_{$tax_type->name}_sitemap" ] = $this->seoframework[ "{$tsf_taxname}_{$robotag}" ] == 1 ? "on" : "off";
			}
		}

		$hash = [ 'sitemap_query_limit' => 'cpseo_items_per_page' ];
		$this->sitemap['cpseo_ping_search_engines']	= isset( $this->seoframework['ping_google'] ) ? 'on' : 'off';
		$this->replace( $hash, $this->seoframework, $this->sitemap );
	}


	/**
	 * Get the actions which can be performed for the plugin.
	 *
	 * @return array
	 */
	public function get_choices() {
		return [
			'settings' => esc_html__( 'Import Settings', 'cpseo' ) . Admin_Helper::get_tooltip( esc_html__( 'Import SEO Framework plugin settings, global meta, sitemap settings, etc.', 'cpseo' ) ),
			'postmeta' => esc_html__( 'Import Post Meta', 'cpseo' ) . Admin_Helper::get_tooltip( esc_html__( 'Import meta information of your posts/pages like the titles, descriptions, robots meta, OpenGraph info, etc.', 'cpseo' ) ),
		];
	}


	/**
	 * Set OpenGraph.
	 *
	 * @param int $post_id Post ID.
	 */
	private function set_post_opengraph( $post_id ) {
		if ( ! empty( $this->seoframework['_open_graph_title'] ) ) {
			update_post_meta( $post_id, 'cpseo_facebook_title', $this->seoframework['_open_graph_title'] );
			update_post_meta( $post_id, 'cpseo_twitter_title', $this->seoframework['_twitter_title'] );
		}

		if ( ! empty( $this->seoframework['_open_graph_description'] ) ) {
			update_post_meta( $post_id, 'cpseo_facebook_description', $this->seoframework['_open_graph_description'] );
			update_post_meta( $post_id, 'cpseo_twitter_description', $this->seoframework['_twitter_description'] );
		}

		$og_thumb = ! empty( $this->seoframework['_social_image_url'] ) ? $this->seoframework['_social_image_url'] : '';
		if ( ! empty( $og_thumb ) ) {
			$this->replace_image( $og_thumb, 'post', 'cpseo_facebook_image', 'cpseo_facebook_image_id', $post_id );
		}
	}

}
