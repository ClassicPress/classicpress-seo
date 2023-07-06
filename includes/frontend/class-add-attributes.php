<?php
/**
 * The class handles adding of attributes to links and images to content.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Frontend
 */

namespace Classic_SEO\Frontend;

use stdClass;
use Classic_SEO\Helper;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Helpers\Str;
use Classic_SEO\Helpers\Url;
use Classic_SEO\Helpers\HTML;

defined( 'ABSPATH' ) || exit;

/**
 * Add Attributes class.
 */
#[\AllowDynamicProperties]
class Add_Attributes {

	use Hooker;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->action( 'wp_head', 'add_attributes', 99 );
	}

	/**
	 * Add nofollow, target, title and alt attributes to link and images.
	 */
	public function add_attributes() {

		// Add rel="nofollow" & target="_blank" for external links.
		$this->add_noopener    = $this->do_filter( 'noopener', true );
		$this->nofollow_link   = Helper::get_settings( 'general.cpseo_nofollow_external_links' );
		$this->nofollow_image  = Helper::get_settings( 'general.cpseo_nofollow_image_links' );
		$this->new_window_link = Helper::get_settings( 'general.cpseo_new_window_external_links' );

		if ( $this->nofollow_link || $this->new_window_link || $this->nofollow_image || $this->add_noopener ) {
			$this->filter( 'the_content', 'add_link_attributes', 11 );
		}

		// Add image title and alt.
		$this->is_alt   = Helper::get_settings( 'general.cpseo_add_img_alt' ) && Helper::get_settings( 'general.cpseo_img_alt_format' ) ? trim( Helper::get_settings( 'general.cpseo_img_alt_format' ) ) : false;
		$this->is_title = Helper::get_settings( 'general.cpseo_add_img_title' ) && Helper::get_settings( 'general.cpseo_img_title_format' ) ? trim( Helper::get_settings( 'general.cpseo_img_title_format' ) ) : false;

		if ( $this->is_alt || $this->is_title ) {
			$this->filter( 'the_content', 'add_img_attributes', 11 );
			$this->filter( 'post_thumbnail_html', 'add_img_attributes', 11 );
			$this->filter( 'woocommerce_single_product_image_thumbnail_html', 'add_img_attributes', 11 );
		}
	}

	/**
	 * Add nofollow and target attributes to link.
	 *
	 * @param  string $content Post content.
	 * @return string
	 */
	public function add_link_attributes( $content ) {
		preg_match_all( '/<(a\s[^>]+)>/', $content, $matches );
		if ( empty( $matches ) || empty( $matches[0] ) ) {
			return $content;
		}

		foreach ( $matches[0] as $link ) {
			$is_dirty = false;
			$attrs    = HTML::extract_attributes( $link );

			if ( ! $this->can_add_attributes( $attrs ) ) {
				continue;
			}

			if ( $this->should_add_nofollow( $attrs['href'] ) ) {
				if ( $this->nofollow_link || ( $this->nofollow_image && $this->is_valid_image( $attrs['href'] ) ) ) {
					$is_dirty = true;
					$this->set_rel_attribute( $attrs, 'nofollow', ( isset( $attrs['rel'] ) && ! Str::contains( 'dofollow', $attrs['rel'] ) && ! Str::contains( 'nofollow', $attrs['rel'] ) ) );
				}
			}

			if ( $this->new_window_link && ! isset( $attrs['target'] ) ) {
				$is_dirty        = true;
				$attrs['target'] = '_blank';
			}

			if ( $this->add_noopener && $this->do_filter( 'noopener/domain', Url::get_domain( $attrs['href'] ) ) ) {
				$is_dirty = true;
				$this->set_rel_attribute( $attrs, 'noopener', ( isset( $attrs['rel'] ) && ! Str::contains( 'noopener', $attrs['rel'] ) ) );
			}

			if ( $is_dirty ) {
				$new     = '<a' . HTML::attributes_to_string( $attrs ) . '>';
				$content = str_replace( $link, $new, $content );
			}
		}

		return $content;
	}

	/**
	 * Set rel attribute.
	 *
	 * @param array   $attrs    Array which hold rel attribute.
	 * @param string  $property Property to add.
	 * @param boolean $append   Append or not.
	 */
	private function set_rel_attribute( &$attrs, $property, $append ) {
		if ( empty( $attrs['rel'] ) ) {
			$attrs['rel'] = $property;
			return;
		}

		if ( $append ) {
			$attrs['rel'] .= ' ' . $property;
		}
	}

	/**
	 * Check if we can do anything
	 *
	 * @param array $attrs Array of link attributes.
	 *
	 * @return boolean
	 */
	private function can_add_attributes( $attrs ) {
		// If link has no href attribute or if the link is not valid then we don't need to do anything.
		if ( empty( $attrs['href'] ) || empty( parse_url( $attrs['href'], PHP_URL_HOST ) ) || ( isset( $attrs['role'] ) && 'button' === $attrs['role'] ) ) {
			return false;
		}

		// Skip if there is no href or it's a hash link like "#id".
		// Skip if relative link.
		// Skip for same domain ignoring sub-domain if any.
		if ( ! Url::is_external( $attrs['href'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if we need to add nofollow for this link, based on "cpseo_nofollow_domains" & "cpseo_nofollow_exclude_domains"
	 *
	 * @param  string $url Link URL.
	 * @return bool
	 */
	private function should_add_nofollow( $url ) {
		if ( ! $this->nofollow_link && ! $this->nofollow_image ) {
			return false;
		}

		$include_domains = $this->get_cpseo_nofollow_domains( 'include' );
		$exclude_domains = $this->get_cpseo_nofollow_domains( 'exclude' );
		$parent_domain   = Url::get_domain( $url );

		// Check if domain is in list.
		if ( ! empty( $include_domains ) ) {
			return Str::contains( $parent_domain, $include_domains );
		}

		// Check if domains is NOT in list.
		if ( ! empty( $exclude_domains ) && Str::contains( $parent_domain, $exclude_domains ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get domain for nofollow
	 *
	 * @param  string $type Type either include or exclude.
	 * @return array
	 */
	private function get_cpseo_nofollow_domains( $type ) {
		static $cpseo_cpseo_nofollow_domains;

		if ( isset( $cpseo_cpseo_nofollow_domains[ $type ] ) ) {
			return $cpseo_cpseo_nofollow_domains[ $type ];
		}

		$setting = 'include' === $type ? 'cpseo_nofollow_domains' : 'cpseo_nofollow_exclude_domains';
		$domains = Helper::get_settings( "general.cpseo_{$setting}" );
		$domains = Str::to_arr_no_empty( $domains );

		$cpseo_cpseo_nofollow_domains[ $type ] = empty( $domains ) ? false : join( ';', $domains );

		return $cpseo_cpseo_nofollow_domains[ $type ];
	}

	/**
	 * Is a valid image url.
	 *
	 * @param string $url Image url.
	 *
	 * @return boolean
	 */
	private function is_valid_image( $url ) {
		foreach ( [ '.jpg', '.jpeg', '.png', '.gif' ] as $ext ) {
			if ( Str::contains( $ext, $url ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Add 'title' and 'alt' attribute to image.
	 *
	 * @param  string $content Post content.
	 * @return string
	 */
	public function add_img_attributes( $content ) {
		if ( empty( $content ) ) {
			return $content;
		}

		$stripped_content = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $content );
		preg_match_all( '/<img ([^>]+)\/?>/iU', $stripped_content, $matches, PREG_SET_ORDER );
		if ( empty( $matches ) ) {
			return $content;
		}

		$post = $this->get_post();
		foreach ( $matches as $image ) {
			$is_dirty = false;
			$attrs    = HTML::extract_attributes( $image[0] );

			if ( ! isset( $attrs['src'] ) ) {
				continue;
			}

			$post->filename = $attrs['src'];
			$this->set_image_attribute( $attrs, 'alt', $this->is_alt, $is_dirty, $post );
			$this->set_image_attribute( $attrs, 'title', $this->is_title, $is_dirty, $post );

			if ( $is_dirty ) {
				$new     = '<img' . HTML::attributes_to_string( $attrs ) . '>';
				$content = str_replace( $image[0], $new, $content );
			}
		}

		return $content;
	}

	/**
	 * Get post object.
	 *
	 * @return object
	 */
	private function get_post() {
		$post = \get_post();
		if ( empty( $post ) ) {
			$post = new stdClass;
		}

		return $post;
	}

	/**
	 * Set image attribute after checking condition.
	 *
	 * @param array   $attrs     Array which hold rel attribute.
	 * @param string  $attribute Attribute to set.
	 * @param boolean $condition Condition to check.
	 * @param boolean $is_dirty  Is dirty variable.
	 * @param object  $post      Post Object.
	 */
	private function set_image_attribute( &$attrs, $attribute, $condition, &$is_dirty, $post ) {
		if ( $condition && empty( $attrs[ $attribute ] ) ) {
			$is_dirty            = true;
			$attrs[ $attribute ] = trim( Helper::replace_vars( $condition, $post ) );
		}
	}
}
