<?php
/**
 * The Rank Math Import Class
 *
 * @since      0.2.0
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\Admin\Importers
 */


namespace ClassicPress_SEO\Admin\Importers;

use ClassicPress_SEO\Helper;
use ClassicPress_SEO\Helpers\DB;
use ClassicPress_SEO\Helpers\WordPress;
use ClassicPress_SEO\Redirections\Redirection;
use ClassicPress_SEO\Admin\Import_Export;

defined( 'ABSPATH' ) || exit;


/**
 * RankMath class.
 */
class RankMath extends Plugin_Importer {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'Rank Math SEO';
	
	/**
	 * Plugin options meta key.
	 *
	 * @var string
	 */
	protected $meta_key = 'rank_math_';
	
	/**
	 * Option keys to import and clean.
	 *
	 * @var array
	 */
	protected $option_keys = [ 'rank-math', 'rank-math-%' ];
	
	/**
	 * Choices keys to import.
	 *
	 * @var array
	 */
	protected $choices = [ 'settings', 'postmeta', 'usermeta', 'redirections' ];

	/**
	 * Table names to drop while cleaning.
	 *
	 * @var array
	 */
	protected $table_names = [ 'rank_math_404_logs', 'rank_math_internal_links', 'rank_math_internal_meta', 'rank_math_redirections', 'rank_math_redirections_cache' ];
	
	
	/**
	 * Import main settings of plugin.
	 *
	 * @return bool
	 */
	protected function settings() {
		$cpseo_backup = new Import_Export();
		$cpseo_backup->run_backup('add');
		
		$rank_math_general       = get_option( 'rank-math-options-general' );
		$rank_math_titles        = get_option( 'rank-math-options-titles' );
		$rank_math_sitemap       = get_option( 'rank-math-options-sitemap' );

		$this->cpseo_update_options( $rank_math_general, 'rank-math-options-general', 'cpseo_' );
		$this->cpseo_update_options( $rank_math_titles, 'rank-math-options-titles', 'cpseo_' );
		$this->cpseo_update_options( $rank_math_sitemap, 'rank-math-options-sitemap', '' );
		
		if ( $this->cpseo_copy_tables() ) {
			return true;
		}
		
		return false;
	}

	
	private function cpseo_update_options($options, $option_name, $prefix) { 
		$exclude_options = [
			'alexa_verify',
			'baidu_verify',
			'bing_verify',
			'google_verify',
			'yandex_verify',
			'norton_verify',
			'pinterest_verify',
			'console_authorization_code',
			'console_caching_control',
			'console_profile',
			'frontend_seo_score',
			'frontend_seo_score_position',
			'frontend_seo_score_post_types',
			'frontend_seo_score_template',
			'htaccess_content',
			'robots_txt_content',
			'support_rank_math',
			'usage_tracking',
			'pt_attachment_robots',
			'social_url_flickr',
			'social_url_foursquare',
			'social_url_myspace',
			'social_url_pinterest',
			'social_url_reddit',
			'social_url_soundcloud',
			'social_url_tumblr',
			'social_url_yelp',
			'tax_post_format_robots',
			'tax_post_tag_robots',
		];
		
		foreach( $options as $key => $item) {
			if ( ! in_array($key, $exclude_options ) ) {
				$cpseo_opts[$prefix.$key] = $item;
			}
		}
		
		if ( $cpseo_opts ) {
			return update_option($option_name, $cpseo_opts);
		}
		return false;
	}
	

	/**
	 * Import post meta of plugin.
	 *
	 * @return array
	 */
	protected function postmeta() {
		global $wpdb;
		
		$this->set_pagination( $this->get_post_ids( true ) );
		
		$rm_metakeys = $wpdb->get_results( "SELECT meta_key, post_id FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE 'rank_math_%' GROUP BY meta_key" );

		$hash = [];
		foreach($rm_metakeys as $rm) {
			$hash[$rm->meta_key] = str_replace('rank_math', 'cpseo', $rm->meta_key);
			$post_ids[] = $rm->post_id;
		}
	
		foreach ( $post_ids as $post ) {
			$post_id = $post;
			$this->replace_meta( $hash, null, $post_id, 'post', false );

			// Cornerstone Content.
			$cornerstone = get_post_meta( $post_id, 'rank_math_pillar_content', true );
			if ( ! empty( $cornerstone ) ) {
				update_post_meta( $post_id, 'cpseo_cornerstone_content', 'on' );
			}
		}

		return $this->get_pagination_arg();
	}
	
	
	/**
	 * Import user meta of plugin.
	 *
	 * @return array
	 */
	protected function usermeta() {
		$this->set_pagination( $this->get_user_ids( true ) );
		$user_ids = $this->get_user_ids();

		$hash = [
			'rank_math_seo_score'						=> 'cpseo_seo_score',
			'rank_math_robots'							=> 'cpseo_robots',
			'rank_math_facebook_enable_image_overlay'	=> 'cpseo_facebook_enable_image_overlay',
			'rank_math_facebook_image_overlay'			=> 'cpseo_facebook_image_overlay',
			'rank_math_twitter_use_facebook'			=> 'cpseo_twitter_use_facebook',
			'rank_math_twitter_card_type'				=> 'cpseo_twitter_card_type',
			'rank_math_twitter_enable_image_overlay'	=> 'cpseo_twitter_enable_image_overlay',
			'rank_math_twitter_image_overlay'			=> 'cpseo_twitter_image_overlay',
			'rank_math_title'							=> 'cpseo_title',
			'rank_math_permalink'						=> 'cpseo_permalink',
			'rank_math_description'						=> 'cpseo_description',
			'rank_math_focus_keyword'					=> 'cpseo_focus_keyword',
			'rank_math_facebook_image_id'				=> 'cpseo_facebook_image_id',
			'rank_math_facebook_image'					=> 'cpseo_facebook_image',
			'rank_math_facebook_author'					=> 'cpseo_facebook_author',
			'rank_math_twitter_author'					=> 'cpseo_twitter_author',
		];

		foreach ( $user_ids as $user ) {
			$this->replace_meta( $hash, null, $user->ID, 'user', false );
		}

		return $this->get_pagination_arg();
	}
	

	/**
	 * Imports redirections data.
	 *
	 * @return array
	 */
	protected function redirections() {
		global $wpdb;
		$ins = 0;
		
		$rm_redirections = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}rank_math_redirections" );
		$this->table = "{$wpdb->prefix}cpseo_redirections";
		$drop_cpseo = $wpdb->get_results( "TRUNCATE {$wpdb->prefix}cpseo_redirections" );
	
		if ($drop_cpseo) {
			$ins = $this->insert($rm_redirections);
		}

		return $ins;
	}
	
	/**
	 * Copies data from RM tables to CPSEO tables
	 *
	 * @return array
	 */
	protected function cpseo_copy_tables() {
		global $wpdb;
		$ins = 0;
		
		foreach( $this->table_names as $rmtable) {
			if ( $rmtable != 'rank_math_redirections_cache' ) {
				$rmdata  = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}{$rmtable}" );
				$cptable = str_replace('rank_math', 'cpseo', $rmtable);
				$this->table = "{$wpdb->prefix}{$cptable}";
				$drop_cpseo = $wpdb->get_results( "TRUNCATE {$wpdb->prefix}{$cptable}" );
				
				if ($drop_cpseo) {
					$ins = $this->insert($rmdata);
				}
			}
		}

		return $ins;
	}
}