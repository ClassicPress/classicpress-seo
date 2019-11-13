<?php
/**
 * Variable model.
 *
 * Replace '%variables%' in strings based on context.
 *
 * @since      0.3.0
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Replace_Variables
 */


namespace Classic_SEO\Replace_Variables;

defined( 'ABSPATH' ) || exit;

/**
 * Variable class.
 */
class Variable {

	/**
	 * Required properties.
	 *
	 * @var array
	 */
	private static $required = [ 'name', 'description', 'variable' ];

	/**
	 * The unique id.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The name of the variabe.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The description of the variable.
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * The variable to use.
	 *
	 * @var string
	 */
	protected $variable;

	/**
	 * The example for the variable.
	 *
	 * @var string
	 */
	protected $example;

	/**
	 * The callback to get the replacement value.
	 *
	 * @var mixed
	 */
	protected $callback;

	/**
	 * Create variable from array.
	 *
	 * @throws \InvalidArgumentException If `$id` is empty.
	 *
	 * @param string $id   Unique id of variable.
	 * @param array  $args Array of values.
	 *
	 * @return Variable
	 */
	public static function from( $id, $args ) {
		if ( empty( $id ) ) {
			throw new \InvalidArgumentException( __( 'The $id variable is required.', 'cpseo' ) );
		}

		$variable          = new Variable;
		$variable->id      = $id;
		$variable->example = isset( $args['example'] ) ? $args['example'] : __( 'Example', 'cpseo' );

		foreach ( self::$required as $key ) {
			if ( ! isset( $args[ $key ] ) ) {
				/* translators: variable name */
				throw new \InvalidArgumentException( sprintf( __( 'The $%1$s is required for variable %2$s.', 'cpseo' ), $key, $id ) );
			}

			$variable->$key = $args[ $key ];
		}

		return $variable;
	}

	/**
	 * Returns the id.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Returns the name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Returns the description.
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Returns the variable.
	 *
	 * @return string
	 */
	public function get_variable() {
		return $this->variable;
	}

	/**
	 * Returns the example.
	 *
	 * @return string
	 */
	public function get_example() {
		return $this->example;
	}

	/**
	 * Set example.
	 *
	 * @param string $example New example.
	 */
	public function set_example( $example ) {
		$this->example = $example;
	}

	/**
	 * Set callback.
	 *
	 * @param mixed $callback New callback.
	 */
	public function set_callback( $callback ) {
		$this->callback = $callback;
	}

	/**
	 * Run callback.
	 *
	 * @param array $args Array of arguments.
	 *
	 * @return mixed
	 */
	public function run_callback( $args ) {
		if ( ! empty( $this->callback ) ) {
			return call_user_func( $this->callback, $args );
		}

		return do_action( 'cpseo/vars/' . $this->get_id(), $args, $this );
	}

	/**
	 * Convert object to array.
	 *
	 * @return array Object as array.
	 */
	public function to_array() {
		$arr = [];
		foreach ( [ 'name', 'description', 'variable', 'example' ] as $key ) {
			$arr[ $key ] = $this->$key;
		}

		return $arr;
	}
}
