<?php
/**
 * The Tools Class.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Status
 */

namespace Classic_SEO\Status;

/**
 * Tools class.
 */
class Tools {

	/**
	 * Register tools rest api hooks.
	 */
	public function hooks() {
		foreach ( $this->get_tools() as $id => $tool ) {
			add_filter( 'cpseo/tools/' . $id, [ $this, $id ] );
		}
	}

	/**
	 * Display Tools data.
	 */
	public function display() {
		?>
		<table class='cpseo-status-table striped cpseo-tools-table widefat'>

			<tbody class='tools'>

				<?php foreach ( $this->get_tools() as $id => $tool ) : ?>
					<tr class='<?php echo sanitize_html_class( $id ); ?>'>
						<th>
							<strong class='name'><?php echo esc_html( $tool['title'] ); ?></strong>
							<p class='description'><?php echo esc_html( $tool['description'] ); ?></p>
						</th>
						<td class='run-tool'>
							<a href='#' class='button button-large tools-action' data-action='<?php echo esc_attr( $id ); ?>' data-confirm="<?php echo isset( $tool['confirm_text'] ) ? esc_attr( $tool['confirm_text'] ) : 'false'; ?>"><?php echo esc_html( $tool['button_text'] ); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>

			</tbody>

		</table>
		<?php
	}

	/**
	 * Get tools.
	 *
	 * @return array
	 */
	private function get_tools() {
		return [
			'clear_transients'    => [
				'title'       => __( 'Classic SEO transients', 'cpseo' ),
				'description' => __( 'This tool will clear all the transients created by the Classic SEO.', 'cpseo' ),
				'button_text' => __( 'Clear transients', 'cpseo' ),
			],

			'clear_seo_analysis'  => [
				'title'       => __( 'Clear seo analysis data', 'cpseo' ),
				'description' => __( 'This tool will clear the SEO Analysis data.', 'cpseo' ),
				'button_text' => __( 'Clear SEO Analysis', 'cpseo' ),
			],

			'delete_links'        => [
				'title'        => __( 'Delete Internal Links data', 'cpseo' ),
				'description'  => __( 'This option will delete ALL the Internal Links data.', 'cpseo' ),
				'confirm_text' => __( 'Are you sure you want to delete Internal Links Data? This action is irreversible.', 'cpseo' ),
				'button_text'  => __( 'Delete Internal Links', 'cpseo' ),
			],

			'delete_redirections' => [
				'title'        => __( 'Delete Redirections rule', 'cpseo' ),
				'description'  => __( 'This option will delete ALL Redirection rules.', 'cpseo' ),
				'confirm_text' => __( 'Are you sure you want to delete all the Redirection Rules? This action is irreversible.', 'cpseo' ),
				'button_text'  => __( 'Delete Redirections', 'cpseo' ),
			],

			'delete_log'          => [
				'title'        => __( 'Delete 404 Log', 'cpseo' ),
				'description'  => __( 'This option will delete ALL 404 monitor log.', 'cpseo' ),
				'confirm_text' => __( 'Are you sure you want to delete the 404 log? This action is irreversible.', 'cpseo' ),
				'button_text'  => __( 'Delete 404 Log', 'cpseo' ),
			],
		];
	}

	/**
	 * Function to clear all the transients.
	 */
	public function clear_transients() {
		global $wpdb;

		$transients = $wpdb->get_col(
			"SELECT `option_name` AS `name`
			FROM  $wpdb->options
			WHERE `option_name` LIKE '%_transient_cpseo%'
			ORDER BY `option_name`"
		);

		if ( empty( $transients ) ) {
			return;
		}

		foreach ( $transients as $transient ) {
			delete_option( $transient );
		}

		return __( 'Classic SEO transients cleared.', 'cpseo' );
	}

	/**
	 * Function to reset SEO Analysis.
	 */
	public function clear_seo_analysis() {
		delete_option( 'cpseo_seo_analysis_results' );

		return  __( 'SEO Analysis data successfully deleted.', 'cpseo' );
	}

	/**
	 * Function to delete the Internal Links data.
	 */
	public function delete_links() {
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cpseo_internal_links;" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cpseo_internal_meta;" );

		return __( 'Internal Links successfully deleted.', 'cpseo' );
	}

	/**
	 * Function to delete 404 log.
	 */
	public function delete_log() {
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cpseo_404_logs;" );

		return __( '404 Log successfully deleted.', 'cpseo' );
	}

	/**
	 * Function to delete the Redirections data.
	 */
	public function delete_redirections() {
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cpseo_redirections;" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}cpseo_redirections_cache;" );

		return __( 'Redirection rules successfully deleted.', 'cpseo' );
	}
}
