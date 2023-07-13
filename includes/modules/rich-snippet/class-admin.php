<?php
/**
 * The Rich Snippet Module
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet

 */

namespace Classic_SEO\RichSnippet;

use Classic_SEO\Helper;
use Classic_SEO\Module\Base;
use Classic_SEO\Admin\Admin_Helper;
use Classic_SEO\Helpers\Arr;
use Classic_SEO\Helpers\Str;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 */
#[\AllowDynamicProperties]
class Admin extends Base {

	/**
	 * The Constructor.
	 */
	public function __construct() {

		$directory = dirname( __FILE__ );
		$this->config([
			'id'        => 'rich-snippet',
			'directory' => $directory,
			'help'      => [
				'title' => esc_html__( 'Rich Snippet', 'cpseo' ),
				'view'  => $directory . '/views/help.php',
			],
		]);
		parent::__construct();

		$this->action( 'cmb2_admin_init', 'enqueue', 50 );
		$this->filter( 'cpseo/metabox/tabs', 'add_metabox_tab' );
		$this->action( 'cpseo/metabox/process_fields', 'save_advanced_meta' );
		$this->action( 'cpseo/post/column/seo_details', 'display_schema_type' );
	}

	/**
	 * Add rich snippet tab to the metabox.
	 *
	 * @param array $tabs Array of tabs.
	 *
	 * @return array
	 */
	public function add_metabox_tab( $tabs ) {

		if ( Admin_Helper::is_term_profile_page() ) {
			return $tabs;
		}

		Arr::insert( $tabs, [
			'richsnippet' => [
				'icon'       => 'dashicons',
				'title'      => esc_html__( 'Rich Snippet', 'cpseo' ),
				'desc'       => esc_html__( 'This tab contains snippet options.', 'cpseo' ),
				'file'       => $this->directory . '/views/metabox-options.php',
				'capability' => 'onpage_snippet',
			],
		], 3 );

		return $tabs;
	}

	/**
	 * Save handler for metadata.
	 *
	 * @param CMB2 $cmb CMB2 instance.
	 */
	public function save_advanced_meta( $cmb ) {
		$instructions = $this->can_save_data( $cmb );
		if ( empty( $instructions ) ) {
			return;
		}

		foreach ( $instructions as $key => $instruction ) {
			if ( ! $instruction['name'] || ! $instruction['text'] || empty( trim( $instruction['name'] ) ) ) {
				unset( $instructions[ $key ] );
			}
		}
		$cmb->data_to_save['cpseo_snippet_recipe_instructions'] = $instructions;
	}

	/**
	 * Display schema type for post
	 *
	 * @param int $post_id The current post ID.
	 */
	public function display_schema_type( $post_id ) {
		$schema = get_post_meta( $post_id, 'cpseo_rich_snippet', true );
		if ( ! $schema ) {
			$post_type = get_post_type( $post_id );
			$schema    = Helper::get_settings( "titles.cpseo_pt_{$post_type}_default_rich_snippet" );
		}

		if ( $schema ) : ?>
			<span class="cpseo-column-display schema-type">
				<strong><?php _e( 'Schema', 'cpseo' ); ?>:</strong>
				<?php echo ucfirst( $schema ); ?>
			</span>
			<?php
		endif;
	}

	/**
	 * Can save metadata.
	 *
	 * @param CMB2 $cmb CMB2 instance.
	 *
	 * @return boolean|array
	 */
	private function can_save_data( $cmb ) {
		if ( isset( $cmb->data_to_save['cpseo_snippet_recipe_instruction_type'] ) && 'HowToSection' !== $cmb->data_to_save['cpseo_snippet_recipe_instruction_type'] ) {
			return false;
		}

		return isset( $cmb->data_to_save['cpseo_snippet_recipe_instructions'] ) ? $cmb->data_to_save['cpseo_snippet_recipe_instructions'] : [];
	}

	/**
	 * Enqueue Styles and Scripts required for metabox.
	 */
	public function enqueue() {
		if ( ! Helper::has_cap( 'onpage_snippet' ) ) {
			return;
		}

		$values = [];
		$cmb    = $this->get_metabox();
		if ( false === $cmb ) {
			return;
		}

		foreach ( $cmb->prop( 'fields' ) as $id => $field_args ) {
			$type = $field_args['type'];
			if ( $this->can_exclude( $id, $type ) ) {
				continue;
			}

			$values[ $this->camelize( $id ) ] = 'group' === $type ? $cmb->get_field( $id )->value :
				$cmb->get_field( $id )->escaped_value();
		}

		$values['snippetType'] = $cmb->get_field( 'cpseo_rich_snippet' )->escaped_value();

		// Default values.
		$post_type                    = \Classic_SEO\CMB2::current_object_type();
		$values['defaultName']        = Helper::get_settings( "titles.cpseo_pt_{$post_type}_default_snippet_name", '' );
		$values['defaultDescription'] = Helper::get_settings( "titles.cpseo_pt_{$post_type}_default_snippet_desc", '' );
		$values['knowledgegraphType'] = Helper::get_settings( 'titles.cpseo_knowledgegraph_type' );

		Helper::add_json( 'richSnippets', $values );
		Helper::add_json( 'hasReviewPosts', ! empty( Helper::get_review_posts() ) );
	}

	/**
	 * Get metabox
	 *
	 * @return bool|CMB2
	 */
	private function get_metabox() {
		if ( Admin_Helper::is_term_profile_page() ) {
			return false;
		}

		return cmb2_get_metabox( 'cpseo_metabox' );
	}

	/**
	 * Can exclude field.
	 *
	 * @param string $id   Field id.
	 * @param string $type Field type.
	 *
	 * @return bool
	 */
	private function can_exclude( $id, $type ) {
		$exclude = [ 'meta_tab_container_open', 'tab_container_open', 'tab_container_close', 'tab', 'raw', 'notice' ];
		return in_array( $type, $exclude, true ) || ! Str::starts_with( 'cpseo_snippet_', $id );
	}

	/**
	 * Convert string to camel case.
	 *
	 * @param string $str String to convert.
	 *
	 * @return string
	 */
	private function camelize( $str ) {
		$sep = '_';
		$str = str_replace( 'cpseo_snippet_', '', $str );
		$str = str_replace( $sep, '', ucwords( $str, $sep ) );

		return lcfirst( $str );
	}
}
