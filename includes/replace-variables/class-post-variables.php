<?php
/**
 * Post variable replacer.
 *
 * @since      0.3.0
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Replace_Variables
 */


namespace Classic_SEO\Replace_Variables;

use Classic_SEO\Post;
use Classic_SEO\Paper\Paper;
use Classic_SEO\Helpers\Str;
use Classic_SEO\Helpers\WordPress;

defined( 'ABSPATH' ) || exit;

/**
 * Post_Variables class.
 */
#[\AllowDynamicProperties]
class Post_Variables extends Advanced_Variables {

	/**
	 * Setup post variables.
	 */
	public function setup_post_variables() {
		$this->args = (object) wp_parse_args( array_filter( (array) $this->get_post() ), $this->get_defaults() );

		$this->register_replacement(
			'title',
			[
				'name'        => esc_html__( 'Post Title', 'cpseo' ),
				'description' => esc_html__( 'Title of the current post/page', 'cpseo' ),
				'variable'    => 'title',
				'example'     => $this->get_title(),
			],
			[ $this, 'get_title' ]
		);

		$this->register_replacement(
			'parent_title',
			[
				'name'        => esc_html__( 'Post Title of parent page', 'cpseo' ),
				'description' => esc_html__( 'Title of the parent page of the current post/page', 'cpseo' ),
				'variable'    => 'parent_title',
				'example'     => esc_html__( 'Example Parent Title', 'cpseo' ),
			],
			[ $this, 'get_parent_title' ]
		);

		$this->register_replacement(
			'excerpt',
			[
				'name'        => esc_html__( 'Post Excerpt', 'cpseo' ),
				'description' => esc_html__( 'Excerpt of the current post (or auto-generated if it does not exist)', 'cpseo' ),
				'variable'    => 'excerpt',
				'example'     => $this->get_excerpt(),
			],
			[ $this, 'get_excerpt' ]
		);

		$this->register_replacement(
			'excerpt_only',
			[
				'name'        => esc_html__( 'Post Excerpt', 'cpseo' ),
				'description' => esc_html__( 'Excerpt of the current post (without auto-generation)', 'cpseo' ),
				'variable'    => 'excerpt_only',
				'example'     => $this->is_post_edit && $this->args->post_excerpt ? $this->args->post_excerpt : esc_html__( 'Post Excerpt Only', 'cpseo' ),
			],
			[ $this, 'get_excerpt_only' ]
		);

		$this->register_replacement(
			'seo_title',
			[
				'name'        => esc_html__( 'SEO Title', 'cpseo' ),
				'description' => esc_html__( 'Custom or Generated SEO Title of the current post/page', 'cpseo' ),
				'variable'    => 'seo_title',
				'example'     => $this->get_title(),
			],
			[ $this, 'get_seo_title' ]
		);

		$this->register_replacement(
			'seo_description',
			[
				'name'        => esc_html__( 'SEO Description', 'cpseo' ),
				'description' => esc_html__( 'Custom or Generated SEO Description of the current post/page', 'cpseo' ),
				'variable'    => 'seo_description',
				'example'     => $this->get_excerpt(),
			],
			[ $this, 'get_seo_description' ]
		);

		$this->setup_post_dates_variables();
		$this->setup_post_category_variables();
		$this->setup_post_tags_variables();
	}

	/**
	 * Setup post dates variables.
	 */
	public function setup_post_dates_variables() {
		$this->register_replacement(
			'date',
			[
				'name'        => esc_html__( 'Date Published', 'cpseo' ),
				'description' => wp_kses_post( __( 'Publication date of the current post/page <strong>OR</strong> specified date on date archives', 'cpseo' ) ),
				'variable'    => 'date',
				'example'     => $this->is_post_edit ? get_the_date() : current_time( get_option( 'date_format' ) ),
			],
			[ $this, 'get_date' ]
		);

		$this->register_replacement(
			'modified',
			[
				'name'        => esc_html__( 'Date Modified', 'cpseo' ),
				'description' => esc_html__( 'Last modification date of the current post/page', 'cpseo' ),
				'variable'    => 'modified',
				'example'     => $this->is_post_edit ? get_the_modified_date() : current_time( get_option( 'date_format' ) ),
			],
			[ $this, 'get_modified' ]
		);

		$this->register_replacement(
			'date_args',
			[
				'name'        => esc_html__( 'Date Published (advanced)', 'cpseo' ),
				'description' => esc_html__( 'Publish date with custom formatting pattern.', 'cpseo' ),
				'variable'    => 'date(F jS, Y)',
				'example'     => date_i18n( 'F jS, Y' ),
			],
			[ $this, 'get_date' ]
		);

		$this->register_replacement(
			'modified_args',
			[
				'name'        => esc_html__( 'Date Modified (advanced)', 'cpseo' ),
				'description' => esc_html__( 'Modified date with custom formatting pattern.', 'cpseo' ),
				'variable'    => 'modified(F jS, Y)',
				'example'     => date_i18n( 'F jS, Y' ),
			],
			[ $this, 'get_modified' ]
		);
	}

	/**
	 * Setup post category variables.
	 */
	public function setup_post_category_variables() {
		$category   = $this->get_category();
		$categories = $this->get_categories();
		$this->register_replacement(
			'category',
			[
				'name'        => esc_html__( 'Post Category', 'cpseo' ),
				'description' => wp_kses_post( __( 'First category (alphabetically) associated to the current post <strong>OR</strong> current category on category archives', 'cpseo' ) ),
				'variable'    => 'category',
				'example'     => $category ? $category : esc_html__( 'Example Category', 'cpseo' ),
			],
			[ $this, 'get_category' ]
		);

		$this->register_replacement(
			'categories',
			[
				'name'        => esc_html__( 'Post Categories', 'cpseo' ),
				'description' => esc_html__( 'Comma-separated list of categories associated to the current post', 'cpseo' ),
				'variable'    => 'categories',
				'example'     => $categories ? $categories : esc_html__( 'Example Category 1, Example Category 2', 'cpseo' ),
			],
			[ $this, 'get_categories' ]
		);

		$this->register_replacement(
			'categories_args',
			[
				'name'        => esc_html__( 'Categories (advanced)', 'cpseo' ),
				'description' => esc_html__( 'Output list of categories associated to the current post, with customization options.', 'cpseo' ),
				'variable'    => 'categories(limit=3&separator= | &exclude=12,23)',
				'example'     => $categories ? $categories : esc_html__( 'Example Category 1, Example Category 2', 'cpseo' ),
			],
			[ $this, 'get_categories' ]
		);
	}

	/**
	 * Setup post tags variables.
	 */
	public function setup_post_tags_variables() {
		$tag  = $this->get_tag();
		$tags = $this->get_tags();
		$this->register_replacement(
			'tag',
			[
				'name'        => esc_html__( 'Post Tag', 'cpseo' ),
				'description' => wp_kses_post( __( 'First tag (alphabetically) associated to the current post <strong>OR</strong> current tag on tag archives', 'cpseo' ) ),
				'variable'    => 'tag',
				'example'     => $tag ? $tag : esc_html__( 'Example Tag', 'cpseo' ),
			],
			[ $this, 'get_tag' ]
		);

		$this->register_replacement(
			'tags',
			[
				'name'        => esc_html__( 'Post Tags', 'cpseo' ),
				'description' => esc_html__( 'Comma-separated list of tags associated to the current post', 'cpseo' ),
				'variable'    => 'tags',
				'example'     => $tags ? $tags : esc_html__( 'Example Tag 1, Example Tag 2', 'cpseo' ),
			],
			[ $this, 'get_tags' ]
		);

		$this->register_replacement(
			'tags_args',
			[
				'name'        => esc_html__( 'Tags (advanced)', 'cpseo' ),
				'description' => esc_html__( 'Output list of tags associated to the current post, with customization options.', 'cpseo' ),
				'variable'    => 'tags(limit=3&separator= | &exclude=12,23)',
				'example'     => $tags ? $tags : esc_html__( 'Example Tag 1 | Example Tag 2', 'cpseo' ),
			],
			[ $this, 'get_tags' ]
		);
	}

	/**
	 * Get the title of the post to use as a replacement.
	 *
	 * @return string|null
	 */
	public function get_title() {
		// Get post type name as Title.
		if ( is_post_type_archive() && ! Post::is_shop_page() ) {
			$post_type = $this->get_queried_post_type();
			return get_post_type_object( $post_type )->labels->name;
		}

		return Str::is_non_empty( $this->args->post_title ) ? stripslashes( $this->args->post_title ) : null;
	}

	/**
	 * Custom or Generated SEO Title
	 *
	 * @return string
	 */
	public function get_seo_title() {
		return Paper::get()->get_title();
	}

	/**
	 * Custom or Generated SEO Description
	 *
	 * @return string
	 */
	public function get_seo_description() {
		return Paper::get()->get_description();
	}

	/**
	 * Get the parent page title of the current page/CPT to use as a replacement.
	 *
	 * @return string|null
	 */
	public function get_parent_title() {
		$on_screen  = is_singular() || is_admin();
		$has_parent = isset( $this->args->post_parent ) && 0 !== $this->args->post_parent;

		return $on_screen && $has_parent ? get_the_title( $this->args->post_parent ) : null;
	}

	/**
	 * Get the post excerpt to use as a replacement. It will be auto-generated if it does not exist.
	 *
	 * @return string|null
	 */
	public function get_excerpt() {
		$object = $this->args;

		// Early Bail!
		if ( empty( $object ) || empty( $object->post_content ) ) {
			return '';
		}

		$keywords     = Post::get_meta( 'focus_keyword', $object->ID );
		$post_content = Paper::should_apply_shortcode() ? do_shortcode( $object->post_content ) : $object->post_content;
		$post_content = \preg_replace( '/<!--[\s\S]*?-->/iu', '', $post_content );
		$post_content = strpos( $post_content, '[' ) === false ? $post_content : preg_replace( '~\[\/?.*?\]~s', '', $post_content );
		$post_content = wpautop( $post_content );
		$post_content = wp_kses( $post_content, [ 'p' => [] ] );

		// 4. Paragraph with the focus keyword.
		if ( ! empty( $keywords ) ) {
			$regex = '/<p>(.*' . str_replace( [ ',', ' ', '/' ], [ '|', '.', '\/' ], $keywords ) . '.*)<\/p>/iu';
			\preg_match_all( $regex, $post_content, $matches );
			if ( isset( $matches[1], $matches[1][0] ) ) {
				return $matches[1][0];
			}
		}

		// 5. The First paragraph of the content.
		\preg_match_all( '/<p>(.*)<\/p>/iu', $post_content, $matches );
		return isset( $matches[1], $matches[1][0] ) ? $matches[1][0] : $post_content;
	}

	/**
	 * Get the post excerpt to use as a replacement (without auto-generating).
	 *
	 * @return string|null
	 */
	public function get_excerpt_only() {
		$has = '' !== $this->args->post_excerpt && ! empty( $this->args->ID ) && ! post_password_required( $this->args->ID );

		return $has ? wp_strip_all_tags( $this->args->post_excerpt ) : null;
	}

	/**
	 * Replace single_month_title deprecated function.
	 *
	 * @return string|false|void False if there's no valid title for the month. Title when retrieving.
	 */
	private function single_month_title( $prefix = '', $display = true ) {
		global $wp_locale;

		$m        = get_query_var( 'm' );
		$year     = get_query_var( 'year' );
		$monthnum = get_query_var( 'monthnum' );

		if ( ! empty( $monthnum ) && ! empty( $year ) ) {
			$my_year  = $year;
			$my_month = $wp_locale->get_month( $monthnum );
		} elseif ( ! empty( $m ) ) {
			$my_year  = substr( $m, 0, 4 );
			$my_month = $wp_locale->get_month( substr( $m, 4, 2 ) );
		}

		if ( empty( $my_month ) ) {
			return false;
		}

		return  ' ' . $my_month . $prefix . $my_year;
	}

	/**
	 * Get the date of the post to use as a replacement.
	 *
	 * @param string $format (Optional) PHP date format.
	 * @return string|null
	 */
	public function get_date( $format = '' ) {
		if ( is_array( $format ) && empty( $format ) ) {
			$format = '';
		}

		if ( '' !== $this->args->post_date ) {
			$format = $format ? $format : get_option( 'date_format' );
			return mysql2date( $format, $this->args->post_date, true );
		}

		if ( ! empty( get_query_var( 'day' ) ) ) {
			return get_the_date( $format );
		}

		$replacement = $this->single_month_title();
		if ( Str::is_non_empty( $replacement ) ) {
			return $replacement;
		}

		return ! empty( get_query_var( 'year' ) ) ? get_query_var( 'year' ) : null;
	}

	/**
	 * Get the post modified time to use as a replacement.
	 *
	 * @param string $format (Optional) PHP date format.
	 * @return string|null
	 */
	public function get_modified( $format = '' ) {
		if ( ! empty( $this->args->post_modified ) ) {
			$format = $format ? $format : get_option( 'date_format' );
			return mysql2date( $format, $this->args->post_modified, true );
		}

		return null;
	}

	/**
	 * Get the post category to use as a replacement.
	 *
	 * @return string|null
	 */
	public function get_category() {
		if ( ! empty( $this->args->ID ) ) {
			$cat = $this->get_terms( $this->args->ID, 'category', true );
			if ( '' !== $cat ) {
				return $cat;
			}
		}

		return ! empty( $this->args->cat_name ) ? $this->args->cat_name : null;
	}

	/**
	 * Get the comma-separate post categories to use as a replacement.
	 *
	 * @param array $args Array of arguments.
	 * @return string|null
	 */
	public function get_categories( $args = [] ) {
		if ( ! empty( $this->args->ID ) ) {
			$cat = $this->get_terms( $this->args->ID, 'category', false, $args );
			if ( '' !== $cat ) {
				return $cat;
			}
		}

		return null;
	}

	/**
	 * Get the current tag to use as a replacement.
	 *
	 * @return string|null
	 */
	public function get_tag() {
		if ( ! empty( $this->args->ID ) ) {
			$tags = $this->get_terms( $this->args->ID, 'post_tag', true );
			if ( '' !== $tags ) {
				return $tags;
			}
		}

		return null;
	}

	/**
	 * Get the current tags to use as a replacement.
	 *
	 * @param array $args Arguments for get_terms().
	 * @return string|null
	 */
	public function get_tags( $args = [] ) {
		if ( ! empty( $this->args->ID ) ) {
			$tags = $this->get_terms( $this->args->ID, 'post_tag', false, $args );
			if ( '' !== $tags ) {
				return $tags;
			}
		}

		return null;
	}

	/**
	 * Default post data.
	 *
	 * @return array
	 */
	private function get_defaults() {
		$defaults = Replacer::$defaults;

		if ( $this->is_post_edit ) {
			$defaults['post_author']  = 'Author Name';
			$defaults['post_content'] = 'Post content';
			$defaults['post_title']   = 'Post Title';
		}

		return $defaults;
	}
}
