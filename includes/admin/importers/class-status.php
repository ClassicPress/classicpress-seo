<?php
/**
 * The Status.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin\Importers
 */


namespace Classic_SEO\Admin\Importers;

use Classic_SEO\Traits\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Status class.
 */
class Status {

	/**
	 * The status.
	 *
	 * @var bool
	 */
	private $status = false;

	/**
	 * The message.
	 *
	 * @var string
	 */
	private $message = '';

	/**
	 * The type of action performed.
	 *
	 * @var string
	 */
	private $action;

	/**
	 * Get the status.
	 *
	 * @return bool Status.
	 */
	public function is_success() {
		return $this->status;
	}

	/**
	 * Get the message.
	 *
	 * @return string Status message.
	 */
	public function get_message() {
		if ( '' === $this->message ) {
			return $this->get_default_message();
		}

		return $this->message;
	}

	/**
	 * Get the action.
	 *
	 * @return string Action type.
	 */
	public function get_action() {
		return $this->action;
	}

	/**
	 * Set the status.
	 *
	 * @param string $status Status.
	 */
	public function set_status( $status ) {
		$this->status = $status;
	}

	/**
	 * Set the message.
	 *
	 * @param string $message Status message.
	 */
	public function set_message( $message ) {
		$this->message = $message;
	}

	/**
	 * Set the action.
	 *
	 * @param string $action Action performing.
	 */
	public function set_action( $action ) {
		$this->action = $action;
	}

	/**
	 * Get default message.
	 *
	 * @return string
	 */
	private function get_default_message() {
		$hash = [
			'settings'     => esc_html__( 'Settings imported successfully.', 'cpseo' ),
			'deactivate'   => esc_html__( 'Plugin deactivated successfully.', 'cpseo' ),
			/* translators: start, end, total */
			'postmeta'     => esc_html__( 'Imported post meta for posts %1$s - %2$s out of %3$s ', 'cpseo' ),
			/* translators: total */
			'termmeta'     => esc_html__( 'Imported term meta for %s terms.', 'cpseo' ),
			/* translators: start, end, total */
			'usermeta'     => esc_html__( 'Imported user meta for users %1$s - %2$s out of %3$s ', 'cpseo' ),
			/* translators: total */
			'redirections' => esc_html__( 'Imported %s redirections.', 'cpseo' ),
		];

		if ( false === $this->is_success() ) {
			$hash = [
				'settings'     => esc_html__( 'Settings import failed.', 'cpseo' ),
				'postmeta'     => esc_html__( 'Posts meta import failed.', 'cpseo' ),
				'termmeta'     => esc_html__( 'Term meta import failed.', 'cpseo' ),
				'usermeta'     => esc_html__( 'User meta import failed.', 'cpseo' ),
				'redirections' => esc_html__( 'No redirection data to import.', 'cpseo' ),
			];
		}

		return isset( $hash[ $this->get_action() ] ) ? $hash[ $this->get_action() ] : '';
	}
}
