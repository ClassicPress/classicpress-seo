<?php
/**
 * The select functions.
 *
 * @since      0.5.0
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Database
 */

namespace Classic_SEO\Database;

/**
 * Select class.
 */
trait Select {

	/**
	 * Set the selected fields
	 *
	 * @param array $fields Fields to select.
	 *
	 * @return self The current query builder.
	 */
	public function select( $fields = '' ) {
		if ( empty( $fields ) ) {
			return $this;
		}

		if ( is_string( $fields ) ) {
			$this->statements['select'][] = $fields;
			return $this;
		}

		foreach ( $fields as $key => $field ) {
			$this->statements['select'][] = is_string( $key ) ? "$key as $field" : $field;
		}

		return $this;
	}

	/**
	 * Shortcut to add a count function
	 *
	 *     ->selectCount('id')
	 *     ->selectCount('id', 'count')
	 *
	 * @param string $field Column name.
	 * @param string $alias (Optional) Column alias.
	 *
	 * @return self The current query builder.
	 */
	public function selectCount( $field = '*', $alias = null ) { // @codingStandardsIgnoreLine
		return $this->selectFunc( 'count', $field, $alias );
	}

	/**
	 * Shortcut to add a sum function
	 *
	 *     ->selectSum('id')
	 *     ->selectSum('id', 'total')
	 *
	 * @param string $field Column name.
	 * @param string $alias (Optional) Column alias.
	 *
	 * @return self The current query builder.
	 */
	public function selectSum( $field, $alias = null ) { // @codingStandardsIgnoreLine
		return $this->selectFunc( 'sum', $field, $alias );
	}

	/**
	 * Shortcut to add a avg function
	 *
	 *     ->selectAvg('id')
	 *     ->selectAvg('id', 'average')
	 *
	 * @param string $field Column name.
	 * @param string $alias (Optional) Column alias.
	 *
	 * @return self The current query builder.
	 */
	public function selectAvg( $field, $alias = null ) { // @codingStandardsIgnoreLine
		return $this->selectFunc( 'avg', $field, $alias );
	}

	/**
	 * Shortcut to add a function
	 *
	 * @param string $func  Function name.
	 * @param string $field Column name.
	 * @param string $alias (Optional) Column alias.
	 *
	 * @return self The current query builder.
	 */
	public function selectFunc( $func, $field, $alias = null ) { // @codingStandardsIgnoreLine
		$field = "$func({$field})";
		if ( ! is_null( $alias ) ) {
			$field .= " as {$alias}";
		}
		$this->statements['select'][] = $field;

		return $this;
	}

	/**
	 * Distinct select setter
	 *
	 * @param bool $distinct Is disticnt.
	 *
	 * @return self The current query builder.
	 */
	public function distinct( $distinct = true ) {
		$this->distinct = $distinct;
		return $this;
	}

	/**
	 * SQL_CALC_FOUND_ROWS select setter
	 *
	 * @param bool $found_rows Should get found rows.
	 *
	 * @return self The current query builder.
	 */
	public function found_rows( $found_rows = true ) {
		$this->found_rows = $found_rows;
		return $this;
	}
}
