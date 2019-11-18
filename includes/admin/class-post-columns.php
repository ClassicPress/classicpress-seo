<?php
/**
 * The admin post columns functionality.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin
 */


namespace Classic_SEO\Admin;

use Classic_SEO\Helper;
use Classic_SEO\Runner;
use Classic_SEO\Traits\Ajax;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Helpers\Param;

defined( 'ABSPATH' ) || exit;

/**
 * Post_Columns class.
 */
class Post_Columns implements Runner {

	use Hooker, Ajax;

	/**
	 * Register hooks.
	 */
	public function hooks() {
		$this->action( 'admin_init', 'init' );
		$this->ajax( 'bulk_edit_columns', 'save' );
	}

	/**
	 * Intialize.
	 */
	public function init() {
		if ( ! Helper::has_cap( 'onpage_general' ) ) {
			return;
		}

		$this->register_post_columns();
		$this->register_media_columns();
		$this->action( 'admin_enqueue_scripts', 'enqueue' );

		// Column Content.
		$this->filter( 'cpseo_title', 'get_column_title', 5 );
		$this->filter( 'cpseo_description', 'get_column_description', 5 );
		$this->filter( 'cpseo_seo_details', 'get_column_seo_details', 5 );
		$this->filter( 'cpseo_seo_score', 'get_column_seo_score', 5 );
		$this->filter( 'cpseo_seo_readability', 'get_column_seo_readability', 5 );
	}

	/**
	 * Save rows.
	 */
	public function save() {
		check_ajax_referer( 'cpseo-ajax-nonce', 'security' );
		$this->has_cap_ajax( 'onpage_general' );
		$rows = Param::post( 'rows', '', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( ! $rows ) {
			$this->error( esc_html__( 'No data found.', 'cpseo' ) );
		}

		foreach ( $rows as $post_id => $data ) {
			$post_id = absint( $post_id );
			if ( ! $post_id ) {
				continue;
			}

			$this->save_row( $post_id, $data );
		}

		$this->success( 'done' );
	}

	/**
	 * Save single row.
	 *
	 * @param int   $post_id Post id.
	 * @param array $data    Post data.
	 */
	private function save_row( $post_id, $data ) {
		foreach ( $data as $key => $value ) {
			$this->save_column( $post_id, $key, $value );
		}
	}

	/**
	 * Save row columns.
	 *
	 * @param int    $post_id Post id.
	 * @param string $column  Column name.
	 * @param string $value   Column value.
	 */
	private function save_column( $post_id, $column, $value ) {
		if ( ! in_array( $column, [ 'focus_keyword', 'title', 'description', 'image_alt', 'image_title' ], true ) ) {
			return;
		}

		if ( 'image_title' === $column ) {
			wp_update_post([
				'ID'         => $post_id,
				'post_title' => $value,
			]);
			return;
		}

		if ( 'focus_keyword' === $column ) {
			$focus_keyword    = get_post_meta( $post_id, 'cpseo_' . $column, true );
			$focus_keyword    = explode( ',', $focus_keyword );
			$focus_keyword[0] = $value;
			$value            = implode( ',', $focus_keyword );
		}

		$column = 'image_alt' === $column ? '_wp_attachment_image_alt' : 'cpseo__' . $column;
		update_post_meta( $post_id, $column, $value );
	}
	
	/**
	 * Register post column hooks.
	 */
	private function register_post_columns() {
		foreach ( Helper::get_allowed_post_types() as $post_type ) {
			$this->filter( "manage_{$post_type}_posts_columns", 'add_columns', 11 );
			$this->action( "manage_{$post_type}_posts_custom_column", 'columns_contents', 11, 2 );
			$this->filter( "manage_edit-{$post_type}_sortable_columns", 'sortable_columns', 11 );

			// Also make them hidden by default.
			$user_id        = get_current_user_id();
			$columns_hidden = (array) get_user_meta( $user_id, "manageedit-{$post_type}columnshidden", true );
			$maybe_hidden   = get_user_meta( $user_id, "manageedit-{$post_type}columnshidden_default", true );

			// Continue if default is already set.
			if ( $maybe_hidden ) {
				continue;
			}

			// Set it to hidden by default.
			$columns_hidden = array_unique( array_merge( $columns_hidden, [ 'cpseo_title', 'cpseo_description' ] ) );
			update_user_meta( $user_id, "manageedit-{$post_type}columnshidden", $columns_hidden );
			update_user_meta( $user_id, "manageedit-{$post_type}columnshidden_default", '1' );
		}
	}

	/**
	 * Register media column hooks.
	 */
	private function register_media_columns() {
		if ( ! Helper::get_settings( 'titles.cpseo_pt_attachment_bulk_editing' ) ) {
			return;
		}

		$this->filter( 'manage_media_columns', 'add_media_columns', 11 );
		$this->action( 'manage_media_custom_column', 'media_contents', 11, 2 );
	}

	/**
	 * Enqueue styles and scripts.
	 */
	public function enqueue() {
		$screen = get_current_screen();

		$allowed_post_types   = Helper::get_allowed_post_types();
		$allowed_post_types[] = 'attachment';
		if ( ( 'edit' !== $screen->base && 'upload' !== $screen->base ) || ! in_array( $screen->post_type, $allowed_post_types, true ) ) {
			return;
		}

		wp_enqueue_style( 'cpseo-post-bulk-edit', cpseo()->plugin_url() . 'assets/admin/css/post-list.css', null, cpseo()->version );
		
		$allow_editing = Helper::get_settings( 'titles.cpseo_pt_' . $screen->post_type . '_bulk_editing' );
		if ( ! $allow_editing || 'readonly' === $allow_editing ) {
			return;
		}

		wp_enqueue_script( 'cpseo-post-bulk-edit', cpseo()->plugin_url() . 'assets/admin/js/post-list.js', null, cpseo()->version, true );
		wp_localize_script( 'cpseo-post-bulk-edit', 'classicSEO', [
			'security'      => wp_create_nonce( 'cpseo-ajax-nonce' ),
			'bulkEditTitle' => esc_attr__( 'Bulk Edit This Field', 'cpseo' ),
			'buttonSaveAll' => esc_attr__( 'Save All Edits', 'cpseo' ),
			'buttonCancel'  => esc_attr__( 'Cancel', 'cpseo' ),
		]);
	}

	/**
	 * Add new columns for SEO title, description and focus keywords.
	 *
	 * @param  array $columns Array of column names.
	 * @return array
	 */
	public function add_columns( $columns ) {
		global $post_type;

		$columns['cpseo_seo_score'] = esc_html__( 'SEO Rating', 'cpseo' );
		$columns['cpseo_seo_readability'] = esc_html__( 'SEO Readability', 'cpseo' );
		$columns['cpseo_seo_details'] = esc_html__( 'SEO Details', 'cpseo' );

		if ( Helper::get_settings( 'titles.cpseo_pt_' . $post_type . '_bulk_editing' ) ) {
			$columns['cpseo_title']       = esc_html__( 'SEO Title', 'cpseo' );
			$columns['cpseo_description'] = esc_html__( 'SEO Desc', 'cpseo' );
		}

		return $columns;
	}

	/**
	 * Make the SEO Score column sortable.
	 *
	 * @param  array $columns Array of column names.
	 * @return array
	 */
	public function sortable_columns( $columns ) {
		$columns['cpseo_seo_score'] = 'cpseo_seo_score';
		$columns['cpseo_seo_readability'] = 'cpseo_seo_readability';

		return $columns;
	}

	/**
	 * Add new columns for Media Alt & Title.
	 *
	 * @param  array $columns An array of column names.
	 * @return array
	 */
	public function add_media_columns( $columns ) {
		$columns['cpseo_image_title'] = esc_html__( 'Title', 'cpseo' );
		$columns['cpseo_image_alt']   = esc_html__( 'Alternative Text', 'cpseo' );

		return $columns;
	}

	/**
	 * Add content for custom column.
	 *
	 * @param string $column_name The name of the column to display.
	 * @param int    $post_id     The current post ID.
	 */
	public function columns_contents( $column_name, $post_id ) {
		do_action( $column_name, $post_id );
	}

	/**
	 * Add content for title column.
	 *
	 * @param int $post_id The current post ID.
	 */
	public function get_column_title( $post_id ) {
		$title     = get_post_meta( $post_id, 'cpseo_title', true );
		$post_type = get_post_type( $post_id );

		if ( ! $title ) {
			$title = Helper::get_settings( "titles.cpseo_pt_{$post_type}_title" );
		}
		?>
		<span class="cpseo-column-display"><?php echo $title; ?></span>
		<span class="cpseo-column-value" data-field="title" contenteditable="true" tabindex="11"><?php echo $title; ?></span>
		<div class="cpseo-column-edit">
			<a href="#" class="cpseo-column-save"><?php esc_html_e( 'Save', 'cpseo' ); ?></a>
			<a href="#" class="button-link-delete cpseo-column-cancel"><?php esc_html_e( 'Cancel', 'cpseo' ); ?></a>
		</div>
		<?php
	}

	/**
	 * Add content for title column.
	 *
	 * @param int $post_id The current post ID.
	 */
	public function get_column_description( $post_id ) {
		$post_type   = get_post_type( $post_id );
		$description = get_post_meta( $post_id, 'cpseo_description', true );

		if ( ! $description ) {
			$description = Helper::get_settings( "titles.cpseo_pt_{$post_type}_description" );
		}
		?>
		<span class="cpseo-column-display"><?php echo $description; ?></span>
		<span class="cpseo-column-value" data-field="description" contenteditable="true" tabindex="11"><?php echo $description; ?></span>
		<div class="cpseo-column-edit">
			<a href="#" class="cpseo-column-save"><?php esc_html_e( 'Save', 'cpseo' ); ?></a>
			<a href="#" class="button-link-delete cpseo-column-cancel"><?php esc_html_e( 'Cancel', 'cpseo' ); ?></a>
		</div>
		<?php
		return;
	}

	/**
	 * Add content for details column.
	 *
	 * @param int $post_id The current post ID.
	 */
	public function get_column_seo_details( $post_id ) {
		$score     = get_post_meta( $post_id, 'cpseo_seo_score', true );
		$keyword   = get_post_meta( $post_id, 'cpseo_focus_keyword', true );
		$keyword   = explode( ',', $keyword )[0];
		$is_cornerstone = get_post_meta( $post_id, 'cpseo_cornerstone_content', true );
		$schema    = get_post_meta( $post_id, 'cpseo_rich_snippet', true );
		$score     = $score ? $score : 0;
		$class     = $this->get_seo_score_class( $score );
		?>
		
		<?php if ( $is_cornerstone ) : ?>
		<label><?php _e( 'Cornerstone', 'cpseo' ); ?>:</label>
		<span class="cpseo-column-display">
			<strong title="Cornerstone"><?php _e( 'Cornerstone', 'cpseo' ); ?>:</strong>
			<span class="dashicons dashicons-yes"></span>
		</span>
		<?php endif; ?>

		<label><?php _e( 'Focus Keyword', 'cpseo' ); ?>:</label>
		<span class="cpseo-column-display">
			<strong title="Focus Keyword"><?php _e( 'Keyword', 'cpseo' ); ?>:</strong>
			<span><?php echo $keyword ? $keyword : esc_html__( 'Not Set', 'cpseo' ); ?></span>
		</span>

		<span class="cpseo-column-value" data-field="focus_keyword" contenteditable="true" tabindex="11">
			<span><?php echo $keyword; ?></span>
		</span>

		<?php if ( $schema ) : ?>
			<span class="cpseo-column-display schema-type">
				<strong><?php _e( 'Schema', 'cpseo' ); ?>:</strong>
				<?php echo ucfirst( $schema ); ?>
			</span>
		<?php endif; ?>

		<div class="cpseo-column-edit">
			<a href="#" class="cpseo-column-save"><?php esc_html_e( 'Save', 'cpseo' ); ?></a>
			<a href="#" class="button-link-delete cpseo-column-cancel"><?php esc_html_e( 'Cancel', 'cpseo' ); ?></a>
		</div>
		<?php
	}
	
	/**
	 * Add content for ratings column.
	 *
	 * @param int $post_id The current post ID.
	 */
	public function get_column_seo_score( $post_id ) {
		$score     = get_post_meta( $post_id, 'cpseo_seo_score', true );
		$score     = $score ? $score : 0;
		$class     = $this->get_seo_score_class( $score );

		if ( ! metadata_exists( 'post', $post_id, 'cpseo_seo_score' ) ) {
			$class = 'no-score';
		}
		?>
		<div class="cpseo-column-display cpseo-score-icon seo-score <?php echo $class; ?> <?php echo ! $score ? 'disabled' : ''; ?>"></div>
		<span class="screen-reader-text cpseo-score-text"><?php echo $score; ?></span>

		<?php
	}
	
	/**
	 * Add content for readability column.
	 *
	 * @param int $post_id The current post ID.
	 */
	public function get_column_seo_readability( $post_id ) {
		$readability     = get_post_meta( $post_id, 'cpseo_seo_readability', true );
		$readability     = $readability ? $readability : 0;
		$class     = $this->get_seo_readability_class( $readability );

		if ( ! metadata_exists( 'post', $post_id, 'cpseo_seo_readability' ) ) {
			$class = 'no-readability';
		}
		?>
		<div class="cpseo-column-display cpseo-readability-icon seo-readability <?php echo $class; ?> <?php echo ! $readability ? 'disabled' : ''; ?>"></div>
		<span class="screen-reader-text cpseo-readability-text"><?php echo $readability; ?></span>
		<?php
	}

	/**
	 * Add content for custom media column.
	 *
	 * @param string $column_name The name of the column to display.
	 * @param int    $post_id     The current post ID.
	 */
	public function media_contents( $column_name, $post_id ) {
		if ( 'cpseo_image_title' === $column_name ) {
			$title = get_the_title( $post_id );
			?>
			<span class="cpseo-column-display"><?php echo $title; ?></span>
			<span class="cpseo-column-value" data-field="image_title" contenteditable="true" tabindex="11"><?php echo $title; ?></span>
			<div class="cpseo-column-edit">
				<a href="#" class="cpseo-column-save"><?php esc_html_e( 'Save', 'cpseo' ); ?></a>
				<a href="#" class="button-link-delete cpseo-column-cancel"><?php esc_html_e( 'Cancel', 'cpseo' ); ?></a>
			</div>
			<?php
			return;
		}

		if ( 'cpseo_image_alt' === $column_name ) {
			$alt = get_post_meta( $post_id, '_wp_attachment_image_alt', true );
			?>
			<span class="cpseo-column-display"><?php echo $alt; ?></span>
			<span class="cpseo-column-value" data-field="image_alt" contenteditable="true" tabindex="11"><?php echo $alt; ?></span>
			<div class="cpseo-column-edit">
				<a href="#" class="cpseo-column-save"><?php esc_html_e( 'Save', 'cpseo' ); ?></a>
				<a href="#" class="button-link-delete cpseo-column-cancel"><?php esc_html_e( 'Cancel', 'cpseo' ); ?></a>
			</div>
			<?php
			return;
		}
	}

	/**
	 * Get SEO score rating string: great/good/bad.
	 *
	 * @param int $score Score.
	 *
	 * @return string
	 */
	private function get_seo_score_class( $score ) {
		if ( $score > 80 ) {
			return 'good';
		}

		if ( $score > 51 && $score < 81 ) {
			return 'ok';
		}

		return 'bad';
	}
	
	/**
	 * Get SEO score rating string: great/good/bad.
	 *
	 * @param int $score Score.
	 *
	 * @return string
	 */
	private function get_seo_readability_class( $readability ) {
		if ( $readability > 80 ) {
			return 'good';
		}

		if ( $readability > 51 && $readability < 81 ) {
			return 'ok';
		}

		return 'bad';
	}
}
