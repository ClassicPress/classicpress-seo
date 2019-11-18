<?php
/**
 * The admin post filters functionality.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin
 */


namespace Classic_SEO\Admin;

use Classic_SEO\Helper;
use Classic_SEO\Runner;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Helpers\Param;

defined( 'ABSPATH' ) || exit;

/**
 * Post_Filters class.
 */
class Post_Filters implements Runner {

	use Hooker;

	/**
	 * Register hooks.
	 */
	public function hooks() {
		$this->action( 'admin_init', 'init' );
	}

	/**
	 * Intialize.
	 */
	public function init() {
		if ( ! Helper::has_cap( 'general' ) ) {
			return;
		}

		$this->filter( 'pre_get_posts', 'posts_by_seo_filters' );
		$this->filter( 'parse_query', 'filter_by_focus_keywords' );
		$this->filter( 'restrict_manage_posts', 'add_seo_filter', 11 );

		foreach ( Helper::get_allowed_post_types() as $post_type ) {
			$this->filter( "views_edit-$post_type", 'add_cornerstone_content_filter_link' );
		}
	}

	/**
	 * Filter posts in admin by Classic SEO's Filter value.
	 *
	 * @param \WP_Query $query The wp_query instance.
	 */
	public function posts_by_seo_filters( $query ) {
		if ( ! $this->can_seo_filters() ) {
			return;
		}

		if ( 'cpseo_seo_score' === $query->get( 'orderby' ) ) {
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', 'cpseo_seo_score' );
			$query->set( 'meta_type', 'numeric' );
		}

		if ( empty( $_GET['cornerstone_content'] ) && empty( $_GET['seo-filter'] ) ) {
			return;
		}

		$meta_query = [];

		// Check for Cornerstone Content filter.
		if ( ! empty( $_GET['cornerstone_content'] ) ) {
			$meta_query[] = [
				'key'   => 'cpseo_cornerstone_content',
				'value' => 'on',
			];
		}

		$this->set_seo_filters( $meta_query );
		$query->set( 'meta_query', $meta_query );
	}

	/**
	 * Filter post in admin by Cornerstone Content.
	 *
	 * @param \WP_Query $query The wp_query instance.
	 */
	public function filter_by_focus_keywords( $query ) {
		if ( ! $this->can_fk_filter() ) {
			return;
		}
		
		if ( $ids = $this->posts_had_reviews() ) { // phpcs:ignore
			$query->set( 'post_type', 'any' );
			$query->set( 'post__in', $ids );
			return;
		}

		$query->set( 'post_status', 'publish' );
		if ( $ids = $this->fk_in_title() ) { // phpcs:ignore
			$query->set( 'post__in', $ids );
			return;
		}

		$focus_keyword = Param::get( 'focus_keyword', '' );
		if ( 1 === absint( $focus_keyword ) ) {
			$query->set(
				'meta_query',
				[
					'relation' => 'AND',
					[
						'key'     => 'cpseo_focus_keyword',
						'compare' => 'NOT EXISTS',
					],
					[
						'relation' => 'OR',
						[
							'key'     => 'cpseo_robots',
							'value'   => 'noindex',
							'compare' => 'NOT LIKE',
						],
						[
							'key'     => 'cpseo_robots',
							'compare' => 'NOT EXISTS',
						],
					],
				]
			);
			return;
		}

		$query->set( 'post_type', 'any' );
		$query->set(
			'meta_query',
			[
				[
					'relation' => 'OR',
					[
						'key'     => 'cpseo_focus_keyword',
						'value'   => $focus_keyword . ',',
						'compare' => 'LIKE',
					],
					[
						'key'     => 'cpseo_focus_keyword',
						'value'   => $focus_keyword,
						'compare' => 'LIKE',
					],
				],
			]
		);
	}

	/**
	 * Add columns for SEO title, description and focus keywords.
	 */
	public function add_seo_filter() {
		global $post_type;

		if ( 'attachment' === $post_type || ! in_array( $post_type, Helper::get_allowed_post_types(), true ) ) {
			return;
		}

		$options  = [
			''          => esc_html__( 'All Posts', 'cpseo' ),
			'great-seo' => esc_html__( 'SEO Score: Great', 'cpseo' ),
			'good-seo'  => esc_html__( 'SEO Score: Good', 'cpseo' ),
			'bad-seo'   => esc_html__( 'SEO Score: Bad', 'cpseo' ),
			'empty-fk'  => esc_html__( 'Focus Keyword Not Set', 'cpseo' ),
			'noindexed' => esc_html__( 'Articles noindexed', 'cpseo' ),
		];
		$selected = Param::get( 'seo-filter' );
		?>
		<select name="seo-filter">
			<?php foreach ( $options as $val => $option ) : ?>
				<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $selected, $val, true ); ?>><?php echo esc_html( $option ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Add view to filter list for Cornerstone Content.
	 *
	 * @param array $views An array of available list table views.
	 */
	public function add_cornerstone_content_filter_link( $views ) {
		global $typenow;

		$current = empty( $_GET['cornerstone_content'] ) ? '' : ' class="current" aria-current="page"';
		$cornerstones = get_posts([
			'post_type'      => $typenow,
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'meta_key'       => 'cpseo_cornerstone_content',
			'meta_value'     => 'on',
		]);

		$views['cornerstone_content'] = sprintf(
			'<a href="%1$s"%2$s>%3$s <span class="count">(%4$s)</span></a>',
			add_query_arg([
				'post_type'      => $typenow,
				'cornerstone_content' => 1,
			]),
			$current,
			esc_html__( 'Cornerstone Content', 'cpseo' ),
			number_format_i18n( count( $cornerstones ) )
		);

		return $views;
	}

	/**
	 * Can apply SEO filters.
	 *
	 * @return bool
	 */
	private function can_seo_filters() {
		$screen = get_current_screen();
		if (
			is_null( $screen ) ||
			'edit' !== $screen->base ||
			! in_array( $screen->post_type, Helper::get_allowed_post_types(), true )
		) {
			return false;
		}

		return true;
	}

	/**
	 * Set SEO filters meta query.
	 *
	 * @param array $query Meta query.
	 */
	private function set_seo_filters( &$query ) {
		$filter = Param::get( 'seo-filter' );
		if ( false === $filter ) {
			return;
		}

		$hash = [
			'empty-fk'  => [
				'key'     => 'cpseo_focus_keyword',
				'compare' => 'NOT EXISTS',
			],
			'bad-seo'   => [
				'key'     => 'cpseo_seo_score',
				'value'   => 50,
				'compare' => '<=',
				'type'    => 'numeric',
			],
			'good-seo'  => [
				'key'     => 'cpseo_seo_score',
				'value'   => [ 51, 80 ],
				'compare' => 'BETWEEN',
			],
			'great-seo' => [
				'key'     => 'cpseo_seo_score',
				'value'   => 80,
				'compare' => '>',
			],
			'noindexed' => [
				'key'     => 'cpseo_robots',
				'value'   => 'noindex',
				'compare' => 'LIKE',
			],
		];

		if ( isset( $hash[ $filter ] ) ) {
			$query[] = $hash[ $filter ];
		}
	}

	/**
	 * Can apply Focus Keyword filter.
	 *
	 * @return bool
	 */
	private function can_fk_filter() {
		$screen = get_current_screen();
		if ( is_null( $screen ) || 'edit' !== $screen->base || ( ! isset( $_GET['focus_keyword'] ) && ! isset( $_GET['fk_in_title'] ) && ! isset( $_GET['review_posts'] ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if Focus Keyword appears in the title.
	 *
	 * @return bool|array
	 */
	private function fk_in_title() {
		global $wpdb;

		$screen      = get_current_screen();
		$fk_in_title = Param::get( 'fk_in_title' );
		if ( ! $fk_in_title ) {
			return false;
		}

		$meta_query = new \WP_Meta_Query([
			[
				'key'     => 'cpseo_focus_keyword',
				'compare' => 'EXISTS',
			],
			[
				'relation' => 'OR',
				[
					'key'     => 'cpseo_robots',
					'value'   => 'noindex',
					'compare' => 'NOT LIKE',
				],
				[
					'key'     => 'cpseo_robots',
					'compare' => 'NOT EXISTS',
				],
			],
		]);

		$meta_query = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		return $wpdb->get_col( "SELECT {$wpdb->posts}.ID FROM $wpdb->posts {$meta_query['join']} WHERE 1=1 {$meta_query['where']} AND {$wpdb->posts}.post_type = '$screen->post_type' AND ({$wpdb->posts}.post_status = 'publish') AND {$wpdb->posts}.post_title NOT REGEXP REPLACE({$wpdb->postmeta}.meta_value, ',', '|')" ); // phpcs:ignore
	}
	
	/**
	 * Check if any posts had Review schema.
	 *
	 * @return bool|array
	 */
	private function posts_had_reviews() {
		global $wpdb;

		$review_posts = Param::get( 'review_posts' );
		if ( ! $review_posts ) {
			return false;
		}

		return get_option( 'cpseo_review_posts', false );
	}
}
