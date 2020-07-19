<?php
/**
 * The Global functionality of the plugin.
 *
 * Defines the functionality loaded on admin.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Rest
 */


namespace Classic_SEO\Rest;

use WP_Error;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Controller;
use Classic_SEO\Helper;
use Classic_SEO\Rest\Helper as RestHelper;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 */
class Admin extends WP_REST_Controller {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace = RestHelper::BASE;
	}

	/**
	 * Registers the routes for the objects of the controller.
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/saveModule',
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'save_module' ],
				'permission_callback' => [ '\\Classic_SEO\\Rest\\Helper', 'can_manage_options' ],
				'args'                => $this->get_save_module_args(),
			]
		);

		register_rest_route(
			$this->namespace,
			'/enableScore',
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'enable_score' ],
				'permission_callback' => [ '\\Classic_SEO\\Rest\\Helper', 'can_manage_options' ],
			]
		);
		
		register_rest_route(
			$this->namespace,
			'/toolsAction',
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'tools_actions' ],
				'permission_callback' => [ '\\Classic_SEO\\Rest\\Helper', 'can_manage_options' ],
			]
		);

	}

	/**
	 * Save module state.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function save_module( WP_REST_Request $request ) {
		$module = $request->get_param( 'module' );
		$state  = $request->get_param( 'state' );

		Helper::update_modules( [ $module => $state ] );

		do_action( 'cpseo/module_changed', $module, $state );
		return true;
	}

	/**
	 * Enable SEO Score.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function enable_score( WP_REST_Request $request ) {
		$settings = wp_parse_args( cpseo()->settings->all_raw(), [
			'general' => '',
		]);

		Helper::update_all_settings( $settings['general'], null, null );
		return true;
	}


	/**
	 * Get save module endpoint arguments.
	 *
	 * @return array
	 */
	private function get_save_module_args() {
		return [
			'module' => [
				'type'              => 'string',
				'required'          => true,
				'description'       => esc_html__( 'Module slug', 'cpseo' ),
				'validate_callback' => [ '\\Classic_SEO\\Rest\\Helper', 'is_param_empty' ],
			],
			'state'  => [
				'type'              => 'string',
				'required'          => true,
				'description'       => esc_html__( 'Module state either on or off', 'cpseo' ),
				'validate_callback' => [ '\\Classic_SEO\\Rest\\Helper', 'is_param_empty' ],
			],
		];
	}

	/**
	 * Tools actions.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function tools_actions( WP_REST_Request $request ) {
		$action = $request->get_param( 'action' );
		return apply_filters( 'cpseo/tools/' . $action, 'Something went wrong.' );
	}
}
