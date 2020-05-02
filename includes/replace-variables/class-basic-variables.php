<?php
/**
 * Basic variable replacer.
 *
 * @since      0.3.0
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Replace_Variables
 */


namespace Classic_SEO\Replace_Variables;

use Classic_SEO\Helper;
use Classic_SEO\Helpers\Str;

defined( 'ABSPATH' ) || exit;

/**
 * Basic_Variables class.
 */
class Basic_Variables extends Cache {

	/**
	 * Hold counter variable data.
	 *
	 * @var array
	 */
	protected $counters = [];

	/**
	 * Setup basic variables.
	 */
	public function setup_basic_variables() {
		$this->register_replacement(
			'sep',
			[
				'name'        => esc_html__( 'Separator Character', 'cpseo' ),
				'description' => esc_html__( 'Separator character, as set in the Title Settings', 'cpseo' ),
				'variable'    => 'sep',
				'example'     => $this->get_sep(),
			],
			[ $this, 'get_sep' ]
		);

		$this->register_replacement(
			'search_query',
			[
				'name'        => esc_html__( 'Search Query', 'cpseo' ),
				'description' => esc_html__( 'Search query (only available on search results page)', 'cpseo' ),
				'variable'    => 'search_query',
				'example'     => esc_html__( 'example search', 'cpseo' ),
			],
			[ $this, 'get_search_query' ]
		);

		$this->register_replacement(
			'count',
			[
				'name'        => esc_html__( 'Counter', 'cpseo' ),
				'description' => esc_html__( 'Starts at 1 and increments by 1.', 'cpseo' ),
				'variable'    => 'count(varname)',
				'example'     => '2',
			],
			[ $this, 'get_count' ]
		);

		$this->register_replacement(
			'filename',
			[
				'name'        => esc_html__( 'File Name', 'cpseo' ),
				'description' => esc_html__( 'File Name of the attachment', 'cpseo' ),
				'variable'    => 'filename',
				'example'     => 'Sunrise at Maldives',
			],
			[ $this, 'get_filename' ]
		);

		$this->setup_site_variables();
		$this->setup_date_variables();
		$this->setup_time_variables();
	}

	/**
	 * Setup site info variables.
	 */
	private function setup_site_variables() {
		$this->register_replacement(
			'sitename',
			[
				'name'        => esc_html__( 'Site Title', 'cpseo' ),
				'description' => esc_html__( 'Title of the site', 'cpseo' ),
				'variable'    => 'sitename',
				'example'     => $this->get_sitename(),
			],
			[ $this, 'get_sitename' ]
		);

		$this->register_replacement(
			'sitedesc',
			[
				'name'        => esc_html__( 'Site Description', 'cpseo' ),
				'description' => esc_html__( 'Description of the site', 'cpseo' ),
				'variable'    => 'sitedesc',
				'example'     => $this->get_sitedesc(),
			],
			[ $this, 'get_sitedesc' ]
		);
	}

	/**
	 * Setup date variables.
	 */
	private function setup_date_variables() {
		$this->register_replacement(
			'currentdate',
			[
				'name'        => esc_html__( 'Current Date', 'cpseo' ),
				'description' => esc_html__( 'Current server date', 'cpseo' ),
				'variable'    => 'currentdate',
				'example'     => $this->get_current_date(),
			],
			[ $this, 'get_current_date' ]
		);

		$this->register_replacement(
			'currentday',
			[
				'name'        => esc_html__( 'Current Day', 'cpseo' ),
				'description' => esc_html__( 'Current server day', 'cpseo' ),
				'variable'    => 'currentday',
				'example'     => $this->get_current_day(),
			],
			[ $this, 'get_current_day' ]
		);

		$this->register_replacement(
			'currentmonth',
			[
				'name'        => esc_html__( 'Current Month', 'cpseo' ),
				'description' => esc_html__( 'Current server month', 'cpseo' ),
				'variable'    => 'currentmonth',
				'example'     => $this->get_current_month(),
			],
			[ $this, 'get_current_month' ]
		);

		$this->register_replacement(
			'currentyear',
			[
				'name'        => esc_html__( 'Current Year', 'cpseo' ),
				'description' => esc_html__( 'Current server year', 'cpseo' ),
				'variable'    => 'currentyear',
				'example'     => $this->get_current_year(),
			],
			[ $this, 'get_current_year' ]
		);
	}

	/**
	 * Setup time variables.
	 */
	private function setup_time_variables() {
		$this->register_replacement(
			'currenttime',
			[
				'name'        => esc_html__( 'Current Time', 'cpseo' ),
				'description' => esc_html__( 'Current server time', 'cpseo' ),
				'variable'    => 'currenttime',
				'example'     => $this->get_current_time(),
			],
			[ $this, 'get_current_time' ]
		);

		$this->register_replacement(
			'currenttime_args',
			[
				'name'        => esc_html__( 'Current Time (advanced)', 'cpseo' ),
				'description' => esc_html__( 'Current server time with custom formatting pattern.', 'cpseo' ),
				'variable'    => 'currenttime(F jS, Y)',
				'example'     => $this->get_current_time( 'F jS, Y' ),
			],
			[ $this, 'get_current_time' ]
		);
	}

	/**
	 * Get the separator to use as a replacement.
	 *
	 * @return string
	 */
	public function get_sep() {
		$sep = $this->do_filter( 'settings/title_separator', Helper::get_settings( 'titles.cpseo_title_separator' ) );

		return htmlentities( $sep, ENT_COMPAT, 'UTF-8', false );
	}

	/**
	 * Get the site name to use as a replacement.
	 *
	 * @return string|null
	 */
	public function get_sitename() {
		if ( $this->in_cache( 'sitename' ) ) {
			return $this->get_cache( 'sitename' );
		}

		$sitename = wp_strip_all_tags( get_bloginfo( 'name' ), true );
		if ( '' !== $sitename ) {
			$this->set_cache( 'sitename', $sitename );
		}

		return $sitename;
	}

	/**
	 * Get the site tag line to use as a replacement.
	 *
	 * @return string|null
	 */
	public function get_sitedesc() {
		if ( $this->in_cache( 'sitedesc' ) ) {
			return $this->get_cache( 'sitedesc' );
		}

		$sitedesc = wp_strip_all_tags( get_bloginfo( 'description' ) );
		if ( '' !== $sitedesc ) {
			$this->set_cache( 'sitedesc', $sitedesc );
		}

		return $sitedesc;
	}

	/**
	 * Get the current search query to use as a replacement.
	 *
	 * @return string|null
	 */
	public function get_search_query() {
		if ( $this->in_cache( 'search_query' ) ) {
			return $this->get_cache( 'search_query' );
		}

		$search = get_search_query();
		if ( '' !== $search ) {
			$this->set_cache( 'search_query', $search );
		}

		return $search;
	}

	/**
	 * Get the counter for the given variable.
	 *
	 * @param string $name The name of field.
	 *
	 * @return string|null
	 */
	public function get_count( $name ) {
		if ( ! is_string( $name ) ) {
			return null;
		}

		if ( ! isset( $this->counters[ $name ] ) ) {
			$this->counters[ $name ] = 0;
		}

		return ++$this->counters[ $name ];
	}

	/**
	 * Get the filename of the attachment to use as a replacement.
	 *
	 * @return string|null
	 */
	public function get_filename() {
		if ( empty( $this->args->filename ) ) {
			return null;
		}

		$name = \pathinfo( $this->args->filename );

		// Remove size if embedded.
		$name = explode( '-', $name['filename'] );
		if ( Str::contains( 'x', end( $name ) ) ) {
			array_pop( $name );
		}

		// Format filename.
		$name = join( ' ', $name );
		$name = trim( str_replace( '_', ' ', $name ) );

		return '' !== $name ? $name : null;
	}

	/**
	 * Get the current time to use as a replacement.
	 *
	 * @param string $format (Optional) PHP date format.
	 *
	 * @return string
	 */
	public function get_current_time( $format = '' ) {
		$format = $format ? $format : get_option( 'time_format' );
		return $this->date_i18n( $format );
	}

	/**
	 * Get the current date to use as a replacement.
	 *
	 * @param string $format (Optional) PHP date format.
	 *
	 * @return string
	 */
	public function get_current_date( $format = '' ) {
		$format = $format ? $format : get_option( 'date_format' );
		return $this->date_i18n( $format );
	}

	/**
	 * Get the current day to use as a replacement.
	 *
	 * @return string
	 */
	public function get_current_day() {
		return $this->date_i18n( 'j' );
	}

	/**
	 * Get the current month to use as a replacement.
	 *
	 * @return string
	 */
	public function get_current_month() {
		return $this->date_i18n( 'F' );
	}

	/**
	 * Get the current year to use as a replacement.
	 *
	 * @return string
	 */
	public function get_current_year() {
		return $this->date_i18n( 'Y' );
	}

	/**
	 * Return localized date.
	 *
	 * @param string $format (Optional) PHP date format.
	 *
	 * @return string
	 */
	private function date_i18n( $format = '' ) {
		$key = 'date_i18n_' . sanitize_key( $format );
		if ( $this->in_cache( $key ) ) {
			return $this->get_cache( $key );
		}

		$date = date_i18n( $format );
		$this->set_cache( $key, $date );

		return $date;
	}
}
