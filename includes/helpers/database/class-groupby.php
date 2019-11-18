<?php
/**
 * The groupby functions.
 *
 * @since      0.5.0
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Database
 */

namespace Classic_SEO\Database;

/**
 * GroupBy class.
 */
trait GroupBy {

	/**
	 * Add an group by statement to the current query
	 *
	 *     ->groupBy('created_at')
	 *
	 * @param array|string $columns Columns.
	 *
	 * @return self The current query builder.
	 */
	public function groupBy( $columns ) { // @codingStandardsIgnoreLine
		if ( is_string( $columns ) ) {
			$columns = $this->argument_to_array( $columns );
		}

		$this->statements['groups'] = $this->statements['groups'] + $columns;

		return $this;
	}

	/**
	 * Generate Having clause
	 *
	 * @param string $column The SQL column.
	 * @param mixed  $param1 Operator or value depending if $param2 isset.
	 * @param mixed  $param2 The value if $param1 is an operator.
	 *
	 * @return self The current query builder.
	 */
	public function having( $column, $param1 = null, $param2 = null ) {
		$this->statements['having'] = $this->generateWhere( $column, $param1, $param2, 'having' );

		return $this;
	}
}
