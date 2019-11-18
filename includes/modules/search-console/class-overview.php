<?php
/**
 * The Search Console Overview
 *
 * @since      0.3.0
 * @package    Classic_SEO
 * @subpackage Classic_SEO\modules
 *
 */

namespace Classic_SEO\Search_Console;

use Classic_SEO\Helper;
use Classic_SEO\Admin\Admin_Helper;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Helpers\Str;

defined( 'ABSPATH' ) || exit;

/**
 * Overview class.
 */
class Overview {

	use Hooker;

	/**
	 * Hold overview data.
	 *
	 * @var array
	 */
	public $data;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->action( 'admin_init', 'get_overview_data' );
	}

	/**
	 * Get overview data.
	 */
	public function get_overview_data() {
		$filters    = Helper::search_console()->get_filters();
		$this->data = DB::get_overview_data( $filters, 'date' );

		Helper::add_json( 'overviewChartData', $this->data->rows );
		Helper::add_json( 'overviewChartDataOld', $this->data->old_rows );
	}

	/**
	 * Display click data.
	 */
	public function display_clicks() {
		?>
		<div class="column clicks">
			<header><?php esc_html_e( 'Total clicks', 'cpseo' ); ?></header>
			<strong><?php echo Str::human_number( $this->data->overview->clicks ) . $this->diff_label( $this->data->overview->clicks, $this->data->overview->old_clicks, true ); ?></strong>
		</div>
		<?php
	}

	/**
	 * Display impressions data.
	 */
	public function display_impressions() {
		?>
		<div class="column impressions">
			<header><?php esc_html_e( 'Total impressions', 'cpseo' ); ?></header>
			<strong><?php echo Str::human_number( $this->data->overview->impressions ) . $this->diff_label( $this->data->overview->impressions, $this->data->overview->old_impressions, true ); ?></strong>
		</div>
		<?php
	}

	/**
	 * Display CTR data.
	 */
	public function display_ctr() {
		?>
		<div class="column ctr">
			<header><?php esc_html_e( 'Avg. CTR', 'cpseo' ); ?></header>
			<strong><?php echo $this->data->overview->ctr . '%' . $this->diff_label( $this->data->overview->ctr, $this->data->overview->old_ctr, false, true ); ?></strong>
		</div>
		<?php
	}

	/**
	 * Display position data.
	 */
	public function display_position() {
		?>
		<div class="column position">
			<header><?php esc_html_e( 'Avg. Position', 'cpseo' ); ?></header>
			<strong><?php echo $this->data->overview->position . $this->diff_label( $this->data->overview->position, $this->data->overview->old_position, false, false, true ); ?></strong>
		</div>
		<?php
	}

	/**
	 * Display keywords data.
	 */
	public function display_keywords() {
		?>
		<div class="column keywords">
			<header><?php esc_html_e( 'Total keywords', 'cpseo' ); ?></header>
			<strong><?php echo Str::human_number( $this->data->overview->keywords ); ?></strong>
		</div>
		<?php
	}

	/**
	 * Display pages data.
	 */
	public function display_pages() {
		?>
		<div class="column pages">
			<header><?php esc_html_e( 'Total pages', 'cpseo' ); ?></header>
			<strong><?php echo Str::human_number( $this->data->overview->pages ); ?></strong>
		</div>
		<?php
	}

	/**
	 * Create difference label for display.
	 *
	 * @param int  $current    Current value.
	 * @param int  $previous   Previous value to compare with.
	 * @param bool $human      Show number in human readable format.
	 * @param bool $percentage Show as percentage.
	 * @param bool $inverted   Invert the result.
	 *
	 * @return string
	 */
	private function diff_label( $current, $previous = 0, $human = false, $percentage = false, $inverted = false ) {

		if ( 0 === $previous ) {
			return '';
		}

		$diff = Admin_Helper::compare_values( $previous, $current );
		if ( 0 === $diff ) {
			return '';
		}

		$downward = $inverted ? 'up' : 'down';
		$upward   = $inverted ? 'down' : 'up';
		$class    = $diff < 0 ? $downward : $upward;

		return sprintf(
			'<span class="compare-value value-%1$s" title="%2$s"><small>%3$s%4$s</small><i class="dashicons dashicons-arrow-%1$s-alt"></i></span>',
			$class,
			/* translators: previous value */
			esc_attr( sprintf( esc_html__( 'Previously: %s', 'cpseo' ), $previous ) ),
			( $diff < 0 ? '' : '+' ) . ( $human ? Str::human_number( $diff ) : $diff ),
			( $percentage ? '%' : '' )
		);
	}
}
