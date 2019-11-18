<?php
/**
 * Sitemaps List
 *
 * @since      0.3.0
 * @package    Classic_SEO
 * @subpackage Classic_SEO\modules
 *
 */

namespace Classic_SEO\Search_Console;

use Classic_SEO\Helper;
use Classic_SEO\Admin\List_Table;

defined( 'ABSPATH' ) || exit;

/**
 * Sitemaps_List class.
 */
class Sitemaps_List extends List_Table {

	/**
	 * The Constructor.
	 */
	public function __construct() {

		parent::__construct(
			[
				'singular' => 'sitemap',
				'plural'   => 'sitemaps',
				'no_items' => esc_html__( 'No sitemaps submitted.', 'cpseo' ),
			]
		);
	}

	/**
	 * Prepares the list of items for displaying.
	 */
	public function prepare_items() {

		$this->set_column_headers();
		$with_index  = ! Helper::search_console()->sitemaps->selected_site_is_domain_property();
		$this->items = Helper::search_console()->sitemaps->get_sitemaps( $with_index );

		$this->set_pagination_args(
			[
				'total_items' => is_array( $this->items ) ? count( $this->items ) : 0,
				'per_page'    => 100,
			]
		);
	}

	/**
	 * Handle column path.
	 *
	 * @param object $item The current item.
	 *
	 * @return string
	 */
	protected function column_path( $item ) {
		return ( empty( $item['isSitemapsIndex'] ) ? '' : '<span class="dashicons dashicons-category"></span>' ) . '<a href="' . $item['path'] . '" target="_blank">' . $item['path'] . '</a>';
	}

	/**
	 * Handle column lastDownloaded.
	 *
	 * @param object $item The current item.
	 *
	 * @return string
	 */
	protected function column_lastDownloaded( $item ) {
		if ( ! empty( $item['lastDownloaded'] ) ) {
			$date = date_parse( $item['lastDownloaded'] );
			$date = date_i18n( 'Y-m-d H:i:s', mktime( $date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year'] ) );
			return $date;
		}
	}

	/**
	 * Handle column items.
	 *
	 * @param object $item The current item.
	 *
	 * @return string
	 */
	protected function column_items( $item ) {
		if ( empty( $item['contents'] ) || ! is_array( $item['contents'] ) ) {
			return;
		}

		$hash = [
			'web'   => [
				'icon'  => 'media-default',
				'title' => esc_html__( 'Pages', 'cpseo' ),
			],
			'image' => [
				'icon'  => 'format-image',
				'title' => esc_html__( 'Images', 'cpseo' ),
			],
			'news'  => [
				'icon'  => 'media-document',
				'title' => esc_html__( 'News', 'cpseo' ),
			],
		];

		$items = '';
		foreach ( $item['contents'] as $contents ) {

			$items .= ! isset( $hash[ $contents['type'] ] ) ? '<span class="cpseo-items-misc">' :
				sprintf(
					'<span title="%1$s"><span class="dashicons dashicons-%2$s"></span> ',
					$hash[ $contents['type'] ]['title'], $hash[ $contents['type'] ]['icon']
				);

			/* translators: content: submitted and indexed */
			$items .= sprintf( wp_kses_post( __( '%1$d <span class="indexed">(%2$d indexed)</span><br>', 'cpseo' ) ), $contents['submitted'], $contents['indexed'] );
			$items .= '</span>';
		}

		return $items;
	}

	/**
	 * Handles the default column output.
	 *
	 * @param object $item        The current item.
	 * @param string $column_name The current column name.
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		if ( 'warnings' === $column_name ) {
			return '<span title="' . esc_html__( 'Warnings', 'cpseo' ) . '">' . $item['warnings'] . '</span>';
		}

		if ( 'errors' === $column_name ) {
			return '<span title="' . esc_html__( 'Errors', 'cpseo' ) . '">' . $item['errors'] . '</span>';
		}

		return print_r( $item, true );
	}

	/**
	 * Get a list of columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return [
			'path'           => esc_html__( 'Path', 'cpseo' ),
			'lastDownloaded' => esc_html__( 'Last Downloaded', 'cpseo' ),
			'items'          => esc_html__( 'Items', 'cpseo' ),
			'warnings'       => esc_html__( 'Warnings', 'cpseo' ) . ' <span class="dashicons dashicons-warning"></span>',
			'errors'         => esc_html__( 'Errors', 'cpseo' ) . ' <span class="dashicons dashicons-dismiss"></span>',
		];
	}

	/**
	 * Generates content for a single row of the table.
	 *
	 * @param object $item The current item.
	 */
	public function single_row( $item ) {
		$classes = [];

		$classes[] = ! empty( $item['isSitemapsIndex'] ) ? 'is-sitemap-index' : 'is-sitemap';

		if ( ! empty( $item['isPending'] ) ) {
			$classes[] = 'is-pending';
		}

		if ( ! empty( $item['errors'] ) ) {
			$classes[] = 'has-errors';
		}

		if ( ! empty( $item['warnings'] ) ) {
			$classes[] = 'has-warnings';
		}

		echo '<tr class="' . join( ' ', $classes ) . '">';
			$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Get refresh button
	 */
	public function get_refresh_button() {
		$url = Helper::get_admin_url(
			'search-console',
			[
				'view'             => 'sitemaps',
				'refresh_sitemaps' => '1',
				'security'         => wp_create_nonce( 'cpseo_refresh_sitemaps' ),
			]
		);
		?>
		<div class="alignleft actions">
			<a href="<?php echo esc_url( $url ); ?>" class="button button-secondary"><?php esc_html_e( 'Refresh Sitemaps', 'cpseo' ); ?></a>
		</div>
		<?php
	}
}
