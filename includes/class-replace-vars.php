<?php
/**
 * Variable replacer.
 *
 * Replace '%variables%' in strings based on context.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Core
 */

namespace Classic_SEO;

use Classic_SEO\Admin\Admin_Helper;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Traits\Replacement;
use Classic_SEO\Helpers\Str;
use Classic_SEO\Helpers\Param;
use Classic_SEO\Helpers\WordPress;

defined( 'ABSPATH' ) || exit;

/**
 * Replace_Vars class.
 */
class Replace_Vars {

	use Hooker, Replacement;

	/**
	 * Register variable replacements.
	 *
	 * @var array
	 */
	protected static $replacements = [];

	/**
	 * Additional variable replacements registered by other plugins/themes.
	 *
	 * @var array
	 */
	protected static $external_replacements = [];

	/**
	 * Hold counter variable data.
	 *
	 * @var array
	 */
	protected static $counters = [];

	/**
	 * Default post data.
	 *
	 * @var array
	 */
	protected $defaults = array(
		'ID'            => '',
		'name'          => '',
		'post_author'   => '',
		'post_content'  => '',
		'post_date'     => '',
		'post_excerpt'  => '',
		'post_modified' => '',
		'post_title'    => '',
		'taxonomy'      => '',
		'term_id'       => '',
		'term404'       => '',
		'filename'      => '',
	);

	/**
	 *  Replace `%variables%` with context-dependent value.
	 *
	 * @param  string $string  The string containing the %variables%.
	 * @param  array  $args    Context object, can be post, taxonomy or term.
	 * @param  array  $exclude Excluded variables won't be replaced.
	 * @return string
	 */
	public function replace( $string, $args = [], $exclude = [] ) {
		$string = strip_tags( $string );

		// Bail early.
		if ( ! Str::contains( '%', $string ) ) {
			return $string;
		}

		if ( Str::ends_with( ' %sep%', $string ) ) {
			$string = str_replace( ' %sep%', '', $string );
		}

		$this->args = (object) wp_parse_args( $args, $this->defaults );

		// Clean $exclude.
		if ( is_array( $exclude ) && ! empty( $exclude ) ) {
			$exclude = array_map( array( __CLASS__, 'remove_var_delimiter' ), $exclude );
		}

		$replacements = [];
		if ( preg_match_all( '/%(([a-z0-9_-]+)\(([^)]*)\)|[^\s]+)%/iu', $string, $matches ) ) {
			$replacements = $this->set_up_replacements( $matches, $exclude );
		}

		/**
		 * Filter: Allow customizing the replacements.
		 *
		 * @param array $replacements
		 */
		$replacements = $this->do_filter( 'replacements', $replacements );

		// Do the replacements.
		if ( is_array( $replacements ) && [] !== $replacements ) {
			$string = str_replace( array_keys( $replacements ), array_values( $replacements ), $string );
		}

		/**
		 * Filter: strip variables which don't have a replacement.
		 *
		 * @param bool $final
		 */
		if ( true === $this->do_filter( 'replacements_remove_nonreplaced', true ) && ( isset( $matches[1] ) && is_array( $matches[1] ) ) ) {
			// Remove non-replaced variables.
			// Don't remove the $exclude variables.
			$remove = array_diff( $matches[1], $exclude );
			$remove = array_map( array( __CLASS__, 'add_var_delimiter' ), $remove );
			$string = str_replace( $remove, '', $string );
		}

		if ( isset( $replacements['%sep%'] ) && Str::is_non_empty( $replacements['%sep%'] ) ) {
			$q_sep  = preg_quote( $replacements['%sep%'], '`' );
			$string = preg_replace( '`' . $q_sep . '(?:\s*' . $q_sep . ')*`u', $replacements['%sep%'], $string );
		}

		return $string;
	}

	/**
	 * Register extra %variables%. For developers.
	 * See cpseo_register_var_replacement().
	 *
	 * @codeCoverageIgnore
	 *
	 * @param  string $var       Variable name, for example %custom%. '%' signs are optional.
	 * @param  mixed  $callback  Replacement callback. Should return value, not output it.
	 * @param  array  $args      Array with additional title, description and example values for the variable.
	 *
	 * @return bool Replacement was registered successfully or not.
	 */
	public static function register_replacement( $var, $callback, $args = [] ) {
		$success = false;

		if ( ! self::is_valid_variable( $var ) ) {
			return false;
		}

		$var = self::remove_var_delimiter( $var );

		if ( ! method_exists( __CLASS__, 'get_' . $var ) ) {
			if ( ! isset( self::$external_replacements[ $var ] ) ) {
				$success                             = true;
				$args['callback']                    = $callback;
				self::$external_replacements[ $var ] = $args;

				return $success;
			}

			trigger_error( esc_html__( 'The variable has already been registered.', 'cpseo' ), E_USER_WARNING );
			return $success;
		}

		trigger_error( esc_html__( 'Standard variables cannot be overridden by registering them again. Use the "cpseo/replacements" filter for this.', 'cpseo' ), E_USER_WARNING );
		return $success;
	}

	/**
	 * Check if variable is valid before further processing.
	 *
	 * @param string $var Variable name.
	 *
	 * @return boolean      Whether the variable is valid or not.
	 */
	public static function is_valid_variable( $var ) {
		if ( ! is_string( $var ) || empty( $var ) ) {
			return false;
		}

		$var = self::remove_var_delimiter( $var );
		if ( false === preg_match( '`^[A-Z0-9_-]+$`i', $var ) ) {
			trigger_error( esc_html__( 'Variable names can only contain alphanumeric characters, underscores and dashes.', 'cpseo' ), E_USER_WARNING );
			return false;
		}

		return true;
	}

	/**
	 * Enqueue styles and scripts.
	 */
	public static function setup_json() {

		// Fetch data for this post.
		if ( Admin_Helper::is_post_edit() ) {
			global $post;
			\setup_postdata( $post );

			$author = get_userdata( $post->post_author );
			if ( $author ) {
				self::$replacements['name']['example'] = $author->display_name;
			}

			self::$replacements['id']['example']           = $post->ID;
			self::$replacements['userid']['example']       = $post->post_author;
			self::$replacements['title']['example']        = get_the_title();
			self::$replacements['date']['example']         = get_the_date();
			self::$replacements['modified']['example']     = get_the_modified_date();
			self::$replacements['excerpt']['example']      = WordPress::strip_shortcodes( self::get_safe_excerpt( $post ) );
			self::$replacements['excerpt_only']['example'] = $post->post_excerpt;

			// Custom Fields.
			$json          = [];
			$custom_fields = get_post_custom( $post->ID );
			if ( ! empty( $custom_fields ) ) {
				foreach ( $custom_fields as $custom_field_name => $custom_field ) {
					if ( substr( $custom_field_name, 0, 1 ) === '_' ) {
						continue;
					}

					$json[ $custom_field_name ] = $custom_field[0];
				}
			}
			Helper::add_json( 'customFields', $json );

			// Custom Taxonomies.
			self::set_custom_taxonomies( $post->ID );
		}

		// Fetch data for this term.
		if ( Admin_Helper::is_term_edit() ) {
			global $taxnow;
			$tag_id = Param::request( 'tag_ID', 0, FILTER_VALIDATE_INT );
			$term   = get_term( $tag_id, $taxnow, OBJECT, 'edit' );

			self::$replacements['term']['example']             = $term->name;
			self::$replacements['term_description']['example'] = term_description( $term );
		}
		Helper::add_json( 'variables', apply_filters( 'cpseo/vars/replacements', array_merge( self::$replacements, self::$external_replacements ) ) );
	}

	/**
	 * Get safe excerpt.
	 *
	 * @param WP_Post $post Post instance.
	 *
	 * @return string
	 */
	public static function get_safe_excerpt( $post ) {
		if ( '' !== $post->post_excerpt ) {
			return strip_tags( $post->post_excerpt );
		} elseif ( '' !== $post->post_content ) {
			return wp_html_excerpt( WordPress::strip_shortcodes( $post->post_content ), 155 );
		}

		return '';
	}

	/**
	 * Set up replacements.
	 */
	public static function setup() {
		global $wp_customize;
		if ( isset( $wp_customize ) ) {
			return;
		}

		if ( empty( self::$replacements ) ) {
			self::set_replacements();
		}

		if ( empty( self::$external_replacements ) ) {
			/**
			 * Action: 'cpseo/vars/register_extra_replacements' - Allows adding extra variables.
			 */
			do_action( 'cpseo/vars/register_extra_replacements' );
		}
	}

	/**
	 * Get list of "swap variables" with descriptions.
	 * Used in the basic JS swap variables function: in meta box preview,
	 * options page title previews, and the variables dropdown.
	 */
	public static function set_replacements() {
		$self = new Replace_Vars();

		$current_user = wp_get_current_user();
		$post_id      = 1;
		$post_title   = esc_html__( 'Hello World', 'cpseo' );
		$posts_array  = get_posts( array( 'posts_per_page' => 1 ) );

		if ( ! empty( $posts_array[0] ) ) {
			$post_id    = $posts_array[0]->ID;
			$post_title = $posts_array[0]->post_title;
		}

		// Basic Variables.
		self::$replacements['title'] = array(
			'name'    => esc_html__( 'Post Title', 'cpseo' ),
			'desc'    => esc_html__( 'Title of the current post/page', 'cpseo' ),
			'example' => $post_title,
		);

		self::$replacements['parent_title'] = array(
			'name'    => esc_html__( 'Post Title of parent page', 'cpseo' ),
			'desc'    => esc_html__( 'Title of the parent page of the current post/page', 'cpseo' ),
			'example' => esc_html__( 'Example Title', 'cpseo' ),
		);

		self::$replacements['sep'] = array(
			'name'    => esc_html__( 'Separator Character', 'cpseo' ),
			'desc'    => esc_html__( 'Separator character, as set in the Title Settings', 'cpseo' ),
			'example' => $self->get_sep(),
		);

		self::$replacements['sitename'] = array(
			'name'    => esc_html__( 'Site Title', 'cpseo' ),
			'desc'    => esc_html__( 'Title of the site', 'cpseo' ),
			'example' => get_bloginfo( 'name' ),
		);

		self::$replacements['sitedesc'] = array(
			'name'    => esc_html__( 'Site Description', 'cpseo' ),
			'desc'    => esc_html__( 'Description of the site', 'cpseo' ),
			'example' => get_bloginfo( 'description' ),
		);

		self::$replacements['date'] = array(
			'name'    => esc_html__( 'Date Published', 'cpseo' ),
			'desc'    => wp_kses_post( __( 'Publication date of the current post/page <strong>OR</strong> specified date on date archives', 'cpseo' ) ),
			'example' => current_time( get_option( 'date_format' ) ),
		);

		self::$replacements['modified'] = array(
			'name'    => esc_html__( 'Date Modified', 'cpseo' ),
			'desc'    => esc_html__( 'Last modification date of the current post/page', 'cpseo' ),
			'example' => current_time( get_option( 'date_format' ) ),
		);

		self::$replacements['excerpt'] = array(
			'name'    => esc_html__( 'Post Excerpt', 'cpseo' ),
			'desc'    => esc_html__( 'Excerpt of the current post (or auto-generated if it does not exist)', 'cpseo' ),
			'example' => esc_html__( 'Post Excerpt', 'cpseo' ),
		);

		self::$replacements['excerpt_only'] = array(
			'name'    => esc_html__( 'Post Excerpt', 'cpseo' ),
			'desc'    => esc_html__( 'Excerpt of the current post (without auto-generation)', 'cpseo' ),
			'example' => esc_html__( 'Post Excerpt', 'cpseo' ),
		);

		self::$replacements['tag'] = array(
			'name'    => esc_html__( 'Post Tag', 'cpseo' ),
			'desc'    => wp_kses_post( __( 'First tag (alphabetically) associated to the current post <strong>OR</strong> current tag on tag archives', 'cpseo' ) ),
			'example' => esc_html__( 'Example Tag', 'cpseo' ),
		);

		self::$replacements['tags'] = array(
			'name'    => esc_html__( 'Post Tags', 'cpseo' ),
			'desc'    => esc_html__( 'Comma-separated list of tags associated to the current post', 'cpseo' ),
			'example' => esc_html__( 'Example Tag 1, Example Tag 2', 'cpseo' ),
		);

		self::$replacements['category'] = array(
			'name'    => esc_html__( 'Post Category', 'cpseo' ),
			'desc'    => wp_kses_post( __( 'First category (alphabetically) associated to the current post <strong>OR</strong> current category on category archives', 'cpseo' ) ),
			'example' => esc_html__( 'Example Category', 'cpseo' ),
		);

		self::$replacements['categories'] = array(
			'name'    => esc_html__( 'Post Categories', 'cpseo' ),
			'desc'    => esc_html__( 'Comma-separated list of categories associated to the current post', 'cpseo' ),
			'example' => esc_html__( 'Example Category 1, Example Category 2', 'cpseo' ),
		);

		self::$replacements['term'] = array(
			'name'    => esc_html__( 'Current Term', 'cpseo' ),
			'desc'    => esc_html__( 'Current term name', 'cpseo' ),
			'example' => esc_html__( 'Example Term', 'cpseo' ),
		);

		self::$replacements['term_description'] = array(
			'name'    => esc_html__( 'Term Description', 'cpseo' ),
			'desc'    => esc_html__( 'Current term description', 'cpseo' ),
			'example' => esc_html__( 'Example Term Description', 'cpseo' ),
		);

		self::$replacements['search_query'] = array(
			'name'    => esc_html__( 'Search Query', 'cpseo' ),
			'desc'    => esc_html__( 'Search query (only available on search results page)', 'cpseo' ),
			'example' => esc_html__( 'example search', 'cpseo' ),
		);

		self::$replacements['name'] = array(
			'name'    => esc_html__( 'Post Author', 'cpseo' ),
			'desc'    => esc_html__( 'Display author\'s nicename of the current post, page or author archive.', 'cpseo' ),
			'example' => $current_user->display_name,
		);

		self::$replacements['user_description'] = array(
			'name'    => esc_html__( 'Author Description', 'cpseo' ),
			'desc'    => esc_html__( 'Author\'s biographical info of the current post, page or author archive.', 'cpseo' ),
			'example' => get_the_author_meta( 'desc' ),
		);

		self::$replacements['filename'] = array(
			'name'    => esc_html__( 'File Name', 'cpseo' ),
			'desc'    => esc_html__( 'File Name of the attachment', 'cpseo' ),
			'example' => 'sunrise at Maldives',
		);

		// Advanced.
		self::$replacements['userid'] = array(
			'name'    => esc_html__( 'Author ID', 'cpseo' ),
			'desc'    => esc_html__( 'Author\'s user id of the current post, page or author archive.', 'cpseo' ),
			'example' => $current_user->ID,
		);

		self::$replacements['id'] = array(
			'name'    => esc_html__( 'Post ID', 'cpseo' ),
			'desc'    => esc_html__( 'ID of the current post/page', 'cpseo' ),
			'example' => $post_id,
		);

		self::$replacements['focuskw'] = array(
			'name'    => esc_html__( 'Focus Keyword', 'cpseo' ),
			'desc'    => esc_html__( 'Focus Keyword of the current post', 'cpseo' ),
			'example' => esc_html__( 'Focus Keyword', 'cpseo' ),
		);

		self::$replacements['page'] = array(
			'name'    => esc_html__( 'Page', 'cpseo' ),
			'desc'    => esc_html__( 'Page number with context (i.e. page 2 of 4). Only displayed on page 2 and above.', 'cpseo' ),
			'example' => ' page 2 of 4',
		);

		self::$replacements['pagetotal'] = array(
			'name'    => esc_html__( 'Max Pages', 'cpseo' ),
			'desc'    => esc_html__( 'Max pages number', 'cpseo' ),
			'example' => '4',
		);

		self::$replacements['pagenumber'] = array(
			'name'    => esc_html__( 'Page Number', 'cpseo' ),
			'desc'    => esc_html__( 'Current page number', 'cpseo' ),
			'example' => '4',
		);

		self::$replacements['currenttime'] = array(
			'name'    => esc_html__( 'Current Time', 'cpseo' ),
			'desc'    => esc_html__( 'Current server time', 'cpseo' ),
			'example' => current_time( get_option( 'time_format' ) ),
		);

		self::$replacements['currentdate'] = array(
			'name'    => esc_html__( 'Current Date', 'cpseo' ),
			'desc'    => esc_html__( 'Current server date', 'cpseo' ),
			'example' => current_time( get_option( 'date_format' ) ),
		);

		self::$replacements['currentday'] = array(
			'name'    => esc_html__( 'Current Day', 'cpseo' ),
			'desc'    => esc_html__( 'Current server day', 'cpseo' ),
			'example' => current_time( 'dS' ),
		);

		self::$replacements['currentmonth'] = array(
			'name'    => esc_html__( 'Current Month', 'cpseo' ),
			'desc'    => esc_html__( 'Current server month', 'cpseo' ),
			'example' => date_i18n( 'F' ),
		);

		self::$replacements['currentyear'] = array(
			'name'    => esc_html__( 'Current Year', 'cpseo' ),
			'desc'    => esc_html__( 'Current server year', 'cpseo' ),
			'example' => current_time( 'Y' ),
		);

		self::$replacements['cpseo_pt_single'] = array(
			'name'    => esc_html__( 'Post Type Name Singular', 'cpseo' ),
			'desc'    => esc_html__( 'Name of current post type (singular)', 'cpseo' ),
			'example' => esc_html__( 'Product', 'cpseo' ),
		);

		self::$replacements['vcpseo_pt_plural'] = array(
			'name'    => esc_html__( 'Post Type Name Plural', 'cpseo' ),
			'desc'    => esc_html__( 'Name of current post type (plural)', 'cpseo' ),
			'example' => esc_html__( 'Products', 'cpseo' ),
		);

		self::$replacements['customfield(field-name)'] = array(
			'name'    => esc_html__( 'Custom Field (advanced)', 'cpseo' ),
			'desc'    => esc_html__( 'Custom field value.', 'cpseo' ),
			'example' => esc_html__( 'Custom field value', 'cpseo' ),
		);

		self::$replacements['date(F jS, Y)'] = array(
			'name'    => esc_html__( 'Date Published (advanced)', 'cpseo' ),
			'desc'    => esc_html__( 'Publish date with custom formatting pattern.', 'cpseo' ),
			'example' => date_i18n( 'F jS, Y' ),
		);

		self::$replacements['modified(F jS, Y)'] = array(
			'name'    => esc_html__( 'Date Modified (advanced)', 'cpseo' ),
			'desc'    => esc_html__( 'Modified date with custom formatting pattern.', 'cpseo' ),
			'example' => date_i18n( 'F jS, Y' ),
		);

		self::$replacements['currenttime(F jS, Y)'] = array(
			'name'    => esc_html__( 'Current Time (advanced)', 'cpseo' ),
			'desc'    => esc_html__( 'Current server time with custom formatting pattern.', 'cpseo' ),
			'example' => current_time( 'F jS, Y' ),
		);

		self::$replacements['categories(limit=3&separator= | &exclude=12,23)'] = array(
			'name'    => esc_html__( 'Categories (advanced)', 'cpseo' ),
			'desc'    => esc_html__( 'Output list of categories associated to the current post, with customization options.', 'cpseo' ),
			'example' => esc_html__( 'Example Category 1 | Example Category 2', 'cpseo' ),
		);

		self::$replacements['tags(limit=3&separator= | &exclude=12,23)'] = array(
			'name'    => esc_html__( 'Tags (advanced)', 'cpseo' ),
			'desc'    => esc_html__( 'Output list of tags associated to the current post, with customization options.', 'cpseo' ),
			'example' => esc_html__( 'Example Tag 1 | Example Tag 2', 'cpseo' ),
		);

		self::$replacements['count(varname)'] = array(
			'name'    => esc_html__( 'Counter', 'cpseo' ),
			'desc'    => esc_html__( 'Starts at 1 and increments by 1.', 'cpseo' ),
			'example' => '2',
		);

		self::$replacements['customterm(taxonomy-name)'] = array(
			'name'    => esc_html__( 'Custom Term (advanced)', 'cpseo' ),
			'desc'    => esc_html__( 'Custom term value.', 'cpseo' ),
			'example' => esc_html__( 'Custom term value', 'cpseo' ),
		);

		self::$replacements['customterm(taxonomy-name_desc)'] = array(
			'name'    => esc_html__( 'Custom Term description', 'cpseo' ),
			'desc'    => esc_html__( 'Custom Term description.', 'cpseo' ),
			'example' => esc_html__( 'Custom Term description.', 'cpseo' ),
		);
	}

	/**
	 * Set custom taxonomies.
	 *
	 * @param  int $post_id The current post ID.
	 * @return void
	 */
	public static function set_custom_taxonomies( $post_id ) {
		$custom_taxonomies = get_post_taxonomies( $post_id );
		if ( empty( $custom_taxonomies ) ) {
			return;
		}

		$json = [];
		foreach ( $custom_taxonomies as $taxonomy ) {
			if ( in_array( $taxonomy, [ 'category', 'post_tag' ], true ) ) {
				continue;
			}

			$name = str_replace( '_', ' ', $taxonomy );
			$name = ucwords( str_replace( '-', ' ', $name ) );
			/* translators: Taxonomy name. */
			$title = sprintf( __( '%s Title', 'cpseo' ), $name );
			/* translators: Taxonomy name. */
			$desc = sprintf( __( '%s Description', 'cpseo' ), $name );

			self::$replacements[ "customterm({$taxonomy})" ] = [
				'name'    => $title,
				'desc'    => esc_html__( 'Custom Term title.', 'cpseo' ),
				'example' => $title,
			];

			self::$replacements[ "customterm({$taxonomy}_desc)" ] = [
				'name'    => $desc,
				'desc'    => esc_html__( 'Custom Term description.', 'cpseo' ),
				'example' => $desc,
			];

			$json[ $taxonomy ]          = $title;
			$json[ "{$taxonomy}_desc" ] = $desc;
		}
		Helper::add_json( 'customTerms', $json );
	}

	/**
	 * Get the current post type.
	 *
	 * @return string Post type name.
	 */
	protected function get_queried_post_type() {
		$post_type = get_post_type();

		if ( false !== $post_type ) {
			return $post_type;
		}

		$post_type = get_query_var( 'post_type' );
		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type );
		}

		return $post_type;
	}

	/**
	 * Get the separator to use as a replacement.
	 *
	 * @return string
	 */
	protected function get_sep() {
		$sep = $this->do_filter( 'settings/cpseo_title_separator', Helper::get_settings( 'titles.cpseo_title_separator' ) );

		return htmlentities( $sep, ENT_COMPAT, 'UTF-8', false );
	}

	/**
	 * Get the parent page title of the current page/CPT to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_parent_title() {
		$replacement = null;

		if ( is_singular() || is_admin() ) {
			if ( isset( $this->args->post_parent ) && 0 !== $this->args->post_parent ) {
				$replacement = get_the_title( $this->args->post_parent );
			}
		}

		return $replacement;
	}

	/**
	 * Get the site name to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_sitename() {
		static $replacement;

		if ( ! isset( $replacement ) ) {
			$sitename = wp_strip_all_tags( get_bloginfo( 'name' ), true );
			if ( '' !== $sitename ) {
				$replacement = $sitename;
			}
		}

		return $replacement;
	}

	/**
	 * Get the site tag line to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_sitedesc() {
		static $replacement;

		if ( ! isset( $replacement ) ) {
			$description = trim( strip_tags( get_bloginfo( 'description' ) ) );
			if ( '' !== $description ) {
				$replacement = $description;
			}
		}

		return $replacement;
	}

	/**
	 * Get the date of the post to use as a replacement.
	 *
	 * @param string $format (Optional) PHP date format.
	 * @return string|null
	 */
	private function get_date( $format = '' ) {
		if ( '' !== $this->args->post_date ) {
			$format = $format ? $format : get_option( 'date_format' );
			return mysql2date( $format, $this->args->post_date, true );
		}

		if ( get_query_var( 'day' ) && get_query_var( 'day' ) !== '' ) {
			return get_the_date( $format );
		}

		if ( single_month_title( ' ', false ) && '' !== single_month_title( ' ', false ) ) {
			return single_month_title( ' ', false );
		}

		if ( '' !== get_query_var( 'year' ) ) {
			return get_query_var( 'year' );
		}

		return null;
	}

	/**
	 * Get the post modified time to use as a replacement.
	 *
	 * @param string $format (Optional) PHP date format.
	 * @return string|null
	 */
	private function get_modified( $format = '' ) {
		$replacement = null;

		if ( ! empty( $this->args->post_modified ) ) {
			$format      = $format ? $format : get_option( 'date_format' );
			$replacement = mysql2date( $format, $this->args->post_modified, true );
		}

		return $replacement;
	}

	/**
	 * Get the post excerpt to use as a replacement. It will be auto-generated if it does not exist.
	 *
	 * @return string|null
	 */
	private function get_excerpt() {
		$replacement = null;

		if ( ! empty( $this->args->ID ) ) {
			if ( '' !== $this->args->post_excerpt ) {
				$replacement = strip_tags( $this->args->post_excerpt );
			} elseif ( '' !== $this->args->post_content ) {
				$replacement = wp_html_excerpt( WordPress::strip_shortcodes( $this->args->post_content ), 155 );
			}
		}

		return $replacement;
	}

	/**
	 * Get the post excerpt to use as a replacement (without auto-generating).
	 *
	 * @return string|null
	 */
	private function get_excerpt_only() {
		$replacement = null;

		if ( ! empty( $this->args->ID ) && '' !== $this->args->post_excerpt ) {
			$replacement = strip_tags( $this->args->post_excerpt );
		}

		return $replacement;
	}

	/**
	 * Get the current tag to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_tag() {
		$replacement = null;

		if ( ! empty( $this->args->ID ) ) {
			$tags = $this->get_terms( $this->args->ID, 'post_tag', true );
			if ( '' !== $tags ) {
				$replacement = $tags;
			}
		}

		return $replacement;
	}

	/**
	 * Get the current tags to use as a replacement.
	 *
	 * @param array $args Arguments for get_terms().
	 * @return string|null
	 */
	private function get_tags( $args = [] ) {
		$replacement = null;

		if ( ! empty( $this->args->ID ) ) {
			$tags = $this->get_terms( $this->args->ID, 'post_tag', false, $args );
			if ( '' !== $tags ) {
				$replacement = $tags;
			}
		}

		return $replacement;
	}

	/**
	 * Get the post category to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_category() {
		$replacement = null;

		if ( ! empty( $this->args->ID ) ) {
			$cat = $this->get_terms( $this->args->ID, 'category', true );
			if ( '' !== $cat ) {
				$replacement = $cat;
			}
		}

		if ( ( ! isset( $replacement ) || '' === $replacement ) && ( isset( $this->args->cat_name ) && ! empty( $this->args->cat_name ) ) ) {
			$replacement = $this->args->cat_name;
		}

		return $replacement;
	}

	/**
	 * Get the comma-separate post categories to use as a replacement.
	 *
	 * @param array $args Array of arguments.
	 * @return string|null
	 */
	private function get_categories( $args = [] ) {
		$replacement = null;

		if ( ! empty( $this->args->ID ) ) {
			$cat = $this->get_terms( $this->args->ID, 'category', false, $args );
			if ( '' !== $cat ) {
				$replacement = $cat;
			}
		}

		return $replacement;
	}

	/**
	 * Get the term name to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_term() {
		$replacement = null;

		if ( is_category() || is_tag() || is_tax() ) {
			global $wp_query;
			$replacement = $wp_query->queried_object->name;
		} elseif ( ! empty( $this->args->taxonomy ) && ! empty( $this->args->name ) ) {
			$replacement = $this->args->name;
		}

		return $replacement;
	}

	/**
	 * Get the term description to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_term_description() {
		if ( is_category() || is_tag() || is_tax() ) {
			global $wp_query;
			return $wp_query->queried_object->description;
		}

		if ( isset( $this->args->term_id ) && ! empty( $this->args->taxonomy ) ) {
			$term_desc = get_term_field( 'description', $this->args->term_id, $this->args->taxonomy );
			if ( '' !== $term_desc ) {
				return trim( strip_tags( $term_desc ) );
			}
		}

		return null;
	}

	/**
	 * Get the current search query to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_search_query() {
		$replacement = null;

		if ( ! isset( $replacement ) ) {
			$search = get_search_query();
			if ( '' !== $search ) {
				$replacement = $search;
			}
		}

		return $replacement;
	}

	/**
	 * Get the post author's user ID to use as a replacement.
	 *
	 * @return string
	 */
	private function get_userid() {
		return ! empty( $this->args->post_author ) ? $this->args->post_author : get_query_var( 'author' );
	}

	/**
	 * Get the post author's "nice name" to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_name() {
		$replacement = null;

		$user_id = $this->get_userid();
		$name    = get_the_author_meta( 'display_name', $user_id );
		if ( '' !== $name ) {
			$replacement = $name;
		}

		return $replacement;
	}

	/**
	 * Get the filename of the attachment to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_filename() {
		if ( empty( $this->args->filename ) ) {
			return null;
		}

		$replacement = null;
		$name        = \pathinfo( $this->args->filename );

		// Remove size if embedded.
		$name = explode( '-', $name['filename'] );
		if ( Str::contains( 'x', end( $name ) ) ) {
			array_pop( $name );
		}

		// Format filename.
		$name = join( ' ', $name );
		$name = trim( str_replace( '_', ' ', $name ) );
		if ( '' !== $name ) {
			$replacement = $name;
		}

		return $replacement;
	}

	/**
	 * Get the post author's user description to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_user_description() {
		$replacement = null;

		$user_id     = $this->get_userid();
		$description = get_the_author_meta( 'description', $user_id );
		if ( '' !== $description ) {
			$replacement = $description;
		}

		return $replacement;
	}

	/**
	 * Get the numeric post ID to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_id() {
		$replacement = null;

		if ( ! empty( $this->args->ID ) ) {
			$replacement = $this->args->ID;
		}

		return $replacement;
	}

	/**
	 * Get the focus keyword to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_focuskw() {
		$replacement = null;

		if ( ! empty( $this->args->ID ) ) {
			$focus_kw = explode( ',', Helper::get_post_meta( 'focus_keyword', $this->args->ID ) )[0];
			if ( '' !== $focus_kw ) {
				$replacement = $focus_kw;
			}
		}

		return $replacement;
	}

	/**
	 * Get the current page number (i.e. "page 2 of 4") to use as a replacement.
	 *
	 * @return string
	 */
	private function get_page() {
		$replacement = null;

		$max  = $this->determine_max_pages();
		$page = $this->determine_page_number();
		$sep  = $this->get_sep();

		if ( $max > 1 && $page > 1 ) {
			/* translators: 1: current page number, 2: total number of pages. */
			$replacement = sprintf( $sep . ' ' . __( 'Page %1$d of %2$d', 'cpseo' ), $page, $max );
		}

		return $replacement;
	}

	/**
	 * Get only the page number (without context) to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_pagenumber() {
		$replacement = null;

		$page = $this->determine_page_number();
		if ( isset( $page ) && $page > 0 ) {
			$replacement = (string) $page;
		}

		return $replacement;
	}

	/**
	 * Get the may page number to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_pagetotal() {
		$replacement = null;

		$max = $this->determine_max_pages();
		if ( isset( $max ) && $max > 0 ) {
			$replacement = (string) $max;
		}

		return $replacement;
	}

	/**
	 * Get the current time to use as a replacement.
	 *
	 * @param string $format (Optional) PHP date format.
	 * @return string
	 */
	private function get_currenttime( $format = '' ) {
		static $replacement;

		if ( ! isset( $replacement ) ) {
			$format      = $format ? $format : get_option( 'time_format' );
			$replacement = date_i18n( $format );
		}

		return $replacement;
	}

	/**
	 * Get the current date to use as a replacement.
	 *
	 * @param string $format (Optional) PHP date format.
	 * @return string
	 */
	private function get_currentdate( $format = '' ) {
		static $replacement;

		if ( ! isset( $replacement ) ) {
			$format      = $format ? $format : get_option( 'date_format' );
			$replacement = date_i18n( $format );
		}

		return $replacement;
	}

	/**
	 * Get the current day to use as a replacement.
	 *
	 * @return string
	 */
	private function get_currentday() {
		static $replacement;

		if ( ! isset( $replacement ) ) {
			$replacement = date_i18n( 'j' );
		}

		return $replacement;
	}

	/**
	 * Get the current month to use as a replacement.
	 *
	 * @return string
	 */
	private function get_currentmonth() {
		static $replacement;

		if ( ! isset( $replacement ) ) {
			$replacement = date_i18n( 'F' );
		}

		return $replacement;
	}

	/**
	 * Get the current year to use as a replacement.
	 *
	 * @return string
	 */
	private function get_currentyear() {
		static $replacement;

		if ( ! isset( $replacement ) ) {
			$replacement = date_i18n( 'Y' );
		}

		return $replacement;
	}

	/**
	 * Get the post type "single" label to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_pt_single() {
		$replacement = null;

		$name = $this->determine_pt_names( 'single' );
		if ( isset( $name ) && '' !== $name ) {
			$replacement = $name;
		}

		return $replacement;
	}

	/**
	 * Get a specific custom field value to use as a replacement.
	 *
	 * @param  string $name The name of the custom field to retrieve.
	 * @return string|null
	 */
	private function get_customfield( $name ) {
		global $post;
		$replacement = null;

		if ( Str::is_non_empty( $name ) ) {
			if ( ( is_singular() || is_admin() ) && ( is_object( $post ) && isset( $post->ID ) ) ) {
				$name = get_post_meta( $post->ID, $name, true );
				if ( '' !== $name ) {
					$replacement = $name;
				}
			}
		}

		return $replacement;
	}


	/**
	 * Get a custom taxonomy term to use as a replacement.
	 *
	 * @param  string $name The name of the taxonomy.
	 * @return string|null
	 */
	private function get_customterm( $name ) {
		if ( Str::is_non_empty( $name ) ) {
			global $post;
			$taxonomy = str_replace( '_desc', '', $name );
			return Str::ends_with( 'desc', $name ) ? $this->get_terms( $post->ID, $taxonomy, true, [], 'description' ) : $this->get_terms( $post->ID, $taxonomy, true, [], 'name' );
		}

		return null;
	}

	/**
	 * Get the counter for the given variable.
	 *
	 * @param  string $name The name of field.
	 * @return string|null
	 */
	private function get_count( $name ) {

		if ( ! isset( self::$counters[ $name ] ) ) {
			self::$counters[ $name ] = 0;
		}

		return ++self::$counters[ $name ];
	}

	/**
	 * Get the post type "plural" label to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_pt_plural() {
		$replacement = null;

		$name = $this->determine_pt_names( 'plural' );
		if ( isset( $name ) && '' !== $name ) {
			$replacement = $name;
		}

		return $replacement;
	}

	/**
	 * Get the replacements for the variables.
	 *
	 * @param  array $matches Regex matches found in the string.
	 * @param  array $exclude Variables that should not be replaced.
	 *
	 * @return array Retrieved replacements.
	 */
	private function set_up_replacements( $matches, $exclude ) {
		$replacements = [];

		foreach ( $matches[1] as $k => $var ) {

			// Don't set up excluded replacements.
			if ( in_array( $var, $exclude, true ) ) {
				continue;
			}

			$args   = [];
			$method = 'get_' . $var;

			// Complex Tags.
			if ( ! empty( $matches[2][ $k ] ) && ! empty( $matches[3][ $k ] ) ) {
				$args   = $this->normalize_args( $matches[3][ $k ] );
				$method = 'get_' . $matches[2][ $k ];
			}

			if ( method_exists( $this, $method ) ) {
				$replacement = $this->$method( $args );
			} elseif ( isset( self::$external_replacements[ $var ] ) && ! is_null( isset( self::$external_replacements[ $var ] ) ) ) {
				$replacement = call_user_func( self::$external_replacements[ $var ]['callback'], $var, $args );
			}

			// If replacement value is null, remove it.
			if ( isset( $replacement ) ) {
				$var                  = self::add_var_delimiter( $var );
				$replacements[ $var ] = $replacement;
			}

			unset( $replacement, $method );
		}

		return $replacements;
	}

	/**
	 * Get the title of the post to use as a replacement.
	 *
	 * @return string|null
	 */
	private function get_title() {
		$replacement = null;

		// Get post type name as Title.
		if ( is_post_type_archive() && ! Post::is_shop_page() ) {
			$post_type   = $this->get_queried_post_type();
			$replacement = get_post_type_object( $post_type )->labels->name;
		} elseif ( Str::is_non_empty( $this->args->post_title ) ) {
			$replacement = stripslashes( $this->args->post_title );
		}

		return $replacement;
	}

	/**
	 * Convert arguments string to arguments array.
	 *
	 * @param  string $string The string that needs to be converted.
	 *
	 * @return array
	 */
	private function normalize_args( $string ) {
		if ( ! Str::contains( '=', $string ) ) {
			return $string;
		}

		return wp_parse_args( $string, [] );
	}

	/**
	 * Get a comma separated list of the post's terms.
	 *
	 * @param int    $id            ID of the post.
	 * @param string $taxonomy      The taxonomy to get the terms from.
	 * @param bool   $return_single Return the first term only.
	 * @param array  $args          Array of arguments.
	 * @param string $field         The term field to return.
	 *
	 * @return string Either a single term field or a comma delimited list of terms.
	 */
	private function get_terms( $id, $taxonomy, $return_single = false, $args = [], $field = 'name' ) {
		$output = '';

		// If we're on a taxonomy archive, use the selected term.
		if ( is_category() || is_tag() || is_tax() ) {
			$term   = $GLOBALS['wp_query']->get_queried_object();
			$output = $term->name;
		}

		if ( ! $output && ! empty( $id ) && ! empty( $taxonomy ) ) {

			$args = wp_parse_args( $args, array(
				'limit'     => 99,
				'separator' => ', ',
				'exclude'   => [],
			) );

			if ( ! empty( $args['exclude'] ) ) {
				$args['exclude'] = array_map( 'trim', explode( ',', $args['exclude'] ) );
			}

			$terms = get_the_terms( $id, $taxonomy );
			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				$count  = 0;
				$output = [];
				foreach ( $terms as $term ) {

					// Limit.
					$count++;
					if ( $count > $args['limit'] ) {
						break;
					}

					// Exclude.
					if ( in_array( $term->term_id, $args['exclude'], true ) ) {
						continue;
					}

					if ( $return_single ) {
						$output = $term->{$field};
						break;
					}

					$output[] = $term->{$field};
				}

				$output = is_array( $output ) ? join( $args['separator'], $output ) : $output;
			}
		}
		unset( $terms, $term );

		/**
		 * Filter: Allows changing the %category% and %tag% terms lists.
		 *
		 * @param string $output The terms list, comma separated.
		 */
		return $this->do_filter( 'vars/terms', $output );
	}
}
