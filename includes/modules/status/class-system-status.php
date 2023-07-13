<?php
/**
 * The System_Status Class.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Status
 */

namespace Classic_SEO\Status;

/**
 * System_Status class.
 */
#[\AllowDynamicProperties]
class System_Status {

	/**
	 * Display Database/Tables Details.
	 */
	public function display() {
		$this->prepare_info();
		$this->display_database_data();
		$this->display_tables_data();
	}

	/**
	 * Display Database Details.
	 */
	private function display_database_data() {
		$hash = [
			'cpseo_version'    => __( 'Classic SEO version', 'cpseo' ),
			'database_version' => __( 'Classic SEO database version', 'cpseo' ),
			'table_prefix'     => __( 'Table Prefix', 'cpseo' ),
			'data_size'        => __( 'Database Data Size', 'cpseo' ),
			'index_size'       => __( 'Database Index Size', 'cpseo' ),
			'total_size'       => __( 'Total Database Size', 'cpseo' ),
		];
		?>
		<table class='cpseo-status-table striped widefat'>
			<thead>
				<tr>
					<th colspan='2'><h2><?php echo esc_html__( 'Database', 'cpseo' ); ?></h2></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $hash as $key => $label ) { ?>
					<tr>
						<td><?php echo esc_html( $label ); ?></td>
						<td><?php echo esc_html( $this->info[ $key ] ); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Display Table details.
	 */
	private function display_tables_data() {
		?>
		<table class='cpseo-status-table striped widefat'>
			<thead>
				<tr>
					<th colspan='2'><h2><?php echo esc_html__( 'Tables', 'cpseo' ); ?></h2></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $this->info['tables'] as $table_name => $data ) { ?>
					<tr>
						<td><?php echo esc_html( $table_name ); ?></td>
						<td>
							<?php echo 'Data: ' . $data['data']; ?> +
							<?php echo 'Index: ' . $data['index']; ?> +
							<?php echo 'Engine: ' . $data['engine']; ?>
						</td>
					</tr>
				<?php } ?>

				<?php $this->missing_tables(); ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Missing Tables Details.
	 */
	private function missing_tables() {
		$core_tables = [
			'cpseo_404_logs',
			'cpseo_redirections',
			'cpseo_redirections_cache',
			'cpseo_internal_links',
			'cpseo_internal_meta',
		];

		$core_tables    = array_map( [ $this, 'add_db_table_prefix' ], $core_tables );
		$missing_tables = array_diff( $core_tables, array_keys( $this->info['tables'] ) );

		if ( empty( $missing_tables ) ) {
			return;
		}

		foreach ( $missing_tables as $table_name ) {
			?>
			<tr>
				<td><?php echo esc_html( $table_name ); ?></td>
				<td>
					<span style="color:red;"><?php echo __( 'Missing', 'cpseo' ); ?></span>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Get Database information.
	 */
	private function prepare_info() {
		global $wpdb;

		$tables        = [];
		$db_index_size = 0;
		$db_data_size  = 0;

		$database_table_information = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT
				table_name AS 'name',
				engine AS 'engine',
				round( ( data_length / 1024 / 1024 ), 2 ) 'data',
				round( ( index_length / 1024 / 1024 ), 2 ) 'index'
				FROM information_schema.TABLES
				WHERE table_schema = %s
				AND table_name LIKE %s
				ORDER BY name ASC;",
				DB_NAME,
				'%cpseo%'
			)
		);

		$tables = [];
		foreach ( $database_table_information as $table ) {
			$tables[ $table->name ] = [
				'data'   => $table->data,
				'index'  => $table->index,
				'engine' => $table->engine,
			];

			$db_data_size  += $table->data;
			$db_index_size += $table->index;
		}

		$this->info = [
			'cpseo_version'    => get_option( 'cpseo_version' ),
			'database_version' => get_option( 'cpseo_db_version' ),
			'table_prefix'     => $wpdb->prefix,
			'tables'           => $tables,
			'data_size'        => $db_data_size . 'MB',
			'index_size'       => $db_index_size . 'MB',
			'total_size'       => $db_data_size + $db_index_size . 'MB',
		];
	}

	/**
	 * Adding prefix to the tables array.
	 *
	 * @param  string $table Table name.
	 *
	 * @return $table Table name with prefix.
	 */
	private function add_db_table_prefix( $table ) {
		global $wpdb;
		return $wpdb->prefix . $table;
	}
}
