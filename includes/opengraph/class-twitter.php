<?php
/**
 * This class handles the Twitter card functionality.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\OpenGraph
 */


namespace Classic_SEO\OpenGraph;

use Classic_SEO\Helper;
use Classic_SEO\Helpers\Str;

defined( 'ABSPATH' ) || exit;

/**
 * Twitter class.
 */
class Twitter extends OpenGraph {

	/**
	 * Network slug.
	 *
	 * @var string
	 */
	public $network = 'twitter';

	/**
	 * Metakey prefix.
	 *
	 * @var string
	 */
	public $prefix = 'twitter';

	/**
	 * Hold site info for twitter card.
	 *
	 * @var string
	 */
	private $site = null;

	/**
	 * Will hold the Twitter card type being created
	 *
	 * @var string
	 */
	private $type;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		/**
		 * Allow changing the Twitter Card type as output in the Twitter card.
		 *
		 * @param string $type
		 */
		if ( false === $this->do_filter( 'opengraph/twitter_card', true ) ) {
			return;
		}

		$this->action( 'cpseo/opengraph/twitter', 'use_facebook', 1 );
		$this->action( 'cpseo/opengraph/twitter', 'type', 5 );
		$this->action( 'cpseo/opengraph/twitter', 'title', 10 );
		$this->action( 'cpseo/opengraph/twitter', 'description', 11 );
		$this->action( 'cpseo/opengraph/twitter', 'website', 14 );
		
		if ( ! post_password_required() ) {
			$this->action( 'cpseo/opengraph/twitter', 'image', 30 );
		}

		if ( is_singular() ) {
			$this->action( 'cpseo/opengraph/twitter', 'article_author', 15 );
		}

		parent::__construct();
	}

	/**
	 * Set use_facebook variable.
	 */
	public function use_facebook() {
		$use_facebook = ( is_category() || is_tag() || is_tax() ) ? Helper::get_term_meta( 'twitter_use_facebook' ) :
			Helper::get_post_meta( 'twitter_use_facebook' );

		if ( $use_facebook ) {
			$this->prefix = 'facebook';
		}
	}

	/**
	 * Display the Twitter card type.
	 *
	 * This defaults to summary but can be filtered using the <code>cpseo_twitter_card_type</code> filter.
	 */
	public function type() {
		$this->determine_card_type();
		$this->sanitize_card_type();

		$this->tag( 'twitter:card', $this->type );

		$remove_tags = false;
		if ( is_singular() && ! is_front_page() && in_array( $this->type, [ 'app', 'player' ], true ) ) {
			$remove_tags = 'app' === $this->type;
			$this->action( 'cpseo/opengraph/twitter', $this->type, 15 );
		}

		$remove_tags = is_date() && in_array( $this->type, [ 'summary', 'summary_large_image' ], true );
		if ( $remove_tags ) {
			$this->remove_tags();
		}
	}

	/**
	 * Output app card.
	 */
	public function app() {

		$this->tag( 'twitter:description', Helper::get_post_meta( 'twitter_app_description' ) );
		$this->tag( 'twitter:app:country', Helper::get_post_meta( 'twitter_app_country' ) );

		// iPhone.
		$this->tag( 'twitter:app:name:iphone', Helper::get_post_meta( 'twitter_app_iphone_name' ) );
		$this->tag( 'twitter:app:id:iphone', Helper::get_post_meta( 'twitter_app_iphone_id' ) );
		$this->tag( 'twitter:app:url:iphone', Helper::get_post_meta( 'twitter_app_iphone_url' ) );

		// iPad.
		$this->tag( 'twitter:app:name:ipad', Helper::get_post_meta( 'twitter_app_ipad_name' ) );
		$this->tag( 'twitter:app:id:ipad', Helper::get_post_meta( 'twitter_app_ipad_id' ) );
		$this->tag( 'twitter:app:url:ipad', Helper::get_post_meta( 'twitter_app_ipad_url' ) );

		// Google Play.
		$this->tag( 'twitter:app:name:googleplay', Helper::get_post_meta( 'twitter_app_googleplay_name' ) );
		$this->tag( 'twitter:app:id:googleplay', Helper::get_post_meta( 'twitter_app_googleplay_id' ) );
		$this->tag( 'twitter:app:url:googleplay', Helper::get_post_meta( 'twitter_app_googleplay_url' ) );
	}

	/**
	 * Output player card.
	 */
	public function player() {
		$this->tag( 'twitter:player', Helper::get_post_meta( 'twitter_player_url' ) );

		$size = Helper::get_post_meta( 'twitter_player_size' );
		if ( $size ) {
			$size = array_map( 'trim', explode( 'x', $size ) );
			if ( isset( $size[1] ) ) {
				$twitter_meta['twitter:player:width']  = (int) $size[0];
				$twitter_meta['twitter:player:height'] = (int) $size[1];
			}
		}
		$this->tag( 'twitter:player:stream', Helper::get_post_meta( 'twitter_player_stream' ) );
		$this->tag( 'twitter:player:stream:content_type', Helper::get_post_meta( 'twitter_player_stream_ctype' ) );
	}

	/**
	 * Output the title.
	 */
	public function title() {
		$this->tag( 'twitter:title', trim( $this->get_title() ) );
	}

	/**
	 * Output the description.
	 */
	public function description() {
		$this->tag( 'twitter:description', trim( $this->get_description() ) );
	}

	/**
	 * Output the Twitter account for the site.
	 */
	public function website() {
		$this->site = $this->get_twitter_id( Helper::get_settings( 'titles.cpseo_social_url_twitter' ) );
		if ( Str::is_non_empty( $this->site ) ) {
			$this->tag( 'twitter:site', '@' . $this->site );
		}
	}

	/**
	 * Output the image for Twitter.
	 *
	 * Only used when OpenGraph is inactive or Summary Large Image card is chosen.
	 */
	public function image() {
		$images = new Image( false, $this );
		foreach ( $images->get_images() as $image_url => $image_meta ) {
			$img_url = $this->get_overlay_image( $this->prefix ) ? admin_url( "admin-ajax.php?action=cpseo_overlay_thumb&id={$image_meta['id']}&type={$this->get_overlay_image( $this->prefix )}" ) : $image_url;
			$this->tag( 'twitter:image', esc_url( $img_url ) );
		}
	}

	/**
	 * Outputs the authors twitter handle.
	 */
	public function article_author() {
		$author = Helper::get_user_meta( 'twitter_author', $GLOBALS['post']->post_author );
		if ( ! $author && ! $author = get_user_meta( $GLOBALS['post']->post_author, 'twitter', true ) ) { // phpcs:ignore
			$author = Helper::get_settings( 'titles.cpseo_twitter_author_names' );
		}
		$author = $this->get_twitter_id( ltrim( trim( $author ), '@' ) );

		if ( Str::is_non_empty( $author ) ) {
			$this->tag( 'twitter:creator', '@' . $author );
		} elseif ( Str::is_non_empty( $this->site ) ) {
			$this->tag( 'twitter:creator', '@' . $this->site );
		}
	}

	/**
	 * Determines the twitter card type for the current page
	 */
	private function determine_card_type() {
		$this->type = Helper::get_post_meta( 'cpseo_twitter_card_type' );
		$this->type = $this->type ? $this->type : Helper::get_settings( 'titles.cpseo_twitter_card_type' );

		/**
		 * Allow changing the Twitter Card type as output in the Twitter card.
		 *
		 * @param string $this->type
		 */
		$this->type = $this->do_filter( 'opengraph/twitter/card_type', $this->type );
	}

	/**
	 * Determines whether the card type is of a type currently allowed by Twitter
	 *
	 * @link https://dev.twitter.com/cards/types
	 */
	private function sanitize_card_type() {
		if ( ! in_array( $this->type, [ 'summary', 'summary_large_image', 'app', 'player' ], true ) ) {
			$this->type = 'summary';
		}
	}

	/**
	 * Checks if the given id is actually an id or a url and if url, distills the id from it.
	 *
	 * Solves issues with filters returning urls and theme's/other plugins also adding a user meta
	 * twitter field which expects url rather than an id (which is what we expect).
	 *
	 * @param  string $id Twitter ID or url.
	 *
	 * @return string|bool Twitter ID or false if it failed to get a valid Twitter ID.
	 */
	private function get_twitter_id( $id ) {
		if ( preg_match( '`([A-Za-z0-9_]{1,25})$`', $id, $match ) ) {
			return $match[1];
		}

		return false;
	}

	/**
	 * Remove archive tags.
	 */
	private function remove_tags() {
		$this->remove_action( 'cpseo/opengraph/twitter', 'title', 10 );
		$this->remove_action( 'cpseo/opengraph/twitter', 'description', 11 );
		$this->remove_action( 'cpseo/opengraph/twitter', 'image', 30 );
	}
}
