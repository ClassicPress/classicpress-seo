<?php
/**
 * The Shortcodes of the plugin.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Frontend
 */

namespace Classic_SEO\Frontend;

use Classic_SEO\Helper;
use Classic_SEO\Paper\Paper;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Traits\Shortcode;

defined( 'ABSPATH' ) || exit;

/**
 * Shortcodes class.
 */
class Shortcodes {

	use Hooker, Shortcode;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->action( 'init', 'init' );
	}

	/**
	 * Initialize.
	 */
	public function init() {

		// Remove Yoast shortcodes.
		$this->remove_shortcode( 'wpseo_address' );
		$this->remove_shortcode( 'wpseo_opening_hours' );

		// Add Yoast compatibility shortcodes.
		$this->add_shortcode( 'wpseo_address', 'yoast_address' );
		$this->add_shortcode( 'wpseo_opening_hours', 'yoast_opening_hours' );

		// Add the Contact shortcode.
		$this->add_shortcode( 'cpseo_contact_info', 'contact_info' );

		// Add the Breadcrumbs shortcode.
		$this->add_shortcode( 'cpseo_breadcrumb', 'breadcrumb' );
	}

	/**
	 * Get the breadcrumbs.
	 *
	 * @param array $args Arguments.
	 *
	 * @return string
	 */
	public function breadcrumb( $args ) {
		if ( ! Helper::get_settings( 'general.cpseo_breadcrumbs' ) ) {
			return;
		}
		return Breadcrumbs::get()->get_breadcrumb( $args );
	}

	/**
	 * Contact info shortcode, displays nicely formatted contact informations.
	 *
	 * @param  array $args Optional. Shortcode arguments - currently only 'show'
	 *                     parameter, which is a comma-separated list of elements to show.
	 * @return string Shortcode output.
	 */
	public function contact_info( $args ) {
		$args = shortcode_atts(
			[
				'show'  => 'all',
				'class' => '',
			],
			$args,
			'contact-info'
		);

		$allowed = $this->get_allowed_info( $args );

		wp_enqueue_style( 'cpseo-contact-info', cpseo()->assets() . 'css/cpseo-contact-info.css', null, cpseo()->version );

		ob_start();
		echo '<div class="' . $this->get_contact_classes( $allowed, $args['class'] ) . '">';

		foreach ( $allowed as $element ) {
			$method = 'display_' . $element;
			if ( method_exists( $this, $method ) ) {
				echo '<div class="cpseo-contact-section cpseo-contact-' . esc_attr( $element ) . '">';
				$this->$method();
				echo '</div>';
			}
		}

		echo '</div>';
		echo '<div class="clear"></div>';

		return ob_get_clean();
	}

	/**
	 * Get allowed info array.
	 *
	 * @param array $args Shortcode arguments - currently only 'show'.
	 *
	 * @return array
	 */
	private function get_allowed_info( $args ) {
		$type = Helper::get_settings( 'titles.cpseo_knowledgegraph_type' );

		$allowed = 'person' === $type ? [ 'name', 'email', 'person_phone', 'address' ] : [ 'name', 'email', 'address', 'hours', 'phone', 'social' ];

		if ( ! empty( $args['show'] ) && 'all' !== $args['show'] ) {
			$allowed = array_intersect( array_map( 'trim', explode( ',', $args['show'] ) ), $allowed );
		}

		return $allowed;
	}

	/**
	 * Get contact info container classes.
	 *
	 * @param  array $allowed     Allowed elements.
	 * @param  array $extra_class Shortcode arguments.
	 * @return string
	 */
	private function get_contact_classes( $allowed, $extra_class ) {
		$classes = [ 'cpseo-contact-info', $extra_class ];
		foreach ( $allowed as $elem ) {
			$classes[] = sanitize_html_class( 'show-' . $elem );
		}
		if ( count( $allowed ) === 1 ) {
			$classes[] = sanitize_html_class( 'show-' . $elem . '-only' );
		}

		return join( ' ', array_filter( $classes ) );
	}

	/**
	 * Output address.
	 */
	private function display_address() {
		$address = Helper::get_settings( 'titles.cpseo_local_address' );
		if ( false === $address ) {
			return;
		}

		$format = nl2br( Helper::get_settings( 'titles.cpseo_local_address_format' ) );
		/**
		 * Allow developer to change the address part format.
		 *
		 * @param string $parts_format String format to output the address part.
		 */
		$parts_format = $this->do_filter( 'shortcode/contact/address_parts_format', '<span class="contact-address-%1$s">%2$s</span>' );

		$hash = [
			'streetAddress'   => 'address',
			'addressLocality' => 'locality',
			'addressRegion'   => 'region',
			'postalCode'      => 'postalcode',
			'addressCountry'  => 'country',
		];
		?>
		<label><?php esc_html_e( 'Address:', 'cpseo' ); ?></label>
		<address>
			<?php
			foreach ( $hash as $key => $tag ) {
				$value = '';
				if ( isset( $address[ $key ] ) && ! empty( $address[ $key ] ) ) {
					$value = sprintf( $parts_format, $tag, $address[ $key ] );
				}

				$format = str_replace( "{{$tag}}", $value, $format );
			}

			echo $format;
			?>
		</address>
		<?php
	}

	/**
	 * Output opening hours.
	 */
	private function display_hours() {
		$hours = Helper::get_settings( 'titles.cpseo_opening_hours' );
		if ( ! isset( $hours[0]['time'] ) ) {
			return;
		}

		$combined = $this->get_hours_combined( $hours );
		$format   = Helper::get_settings( 'titles.cpseo_opening_hours_format' );
		?>
		<label><?php esc_html_e( 'Hours:', 'cpseo' ); ?></label>
		<div class="cpseo-contact-hours-details">
			<?php
			foreach ( $combined as $time => $days ) {
				if ( $format ) {
					$hours = explode( '-', $time );
					$time  = isset( $hours[1] ) ? date_i18n( 'g:i a', strtotime( $hours[0] ) ) . '-' . date_i18n( 'g:i a', strtotime( $hours[1] ) ) : $time;
				}
				$time = str_replace( '-', ' &ndash; ', $time );

				printf(
					'<div class="cpseo-opening-hours"><span class="cpseo-opening-days">%1$s</span><span class="cpseo-opening-time">%2$s</span></div>',
					join( ', ', $days ), $time
				);
			}
			?>
		</div>
		<?php
	}

	/**
	 * Combine hours in an hour
	 *
	 * @param  array $hours Hours to combine.
	 * @return array
	 */
	private function get_hours_combined( $hours ) {
		$combined = [];

		foreach ( $hours as $hour ) {
			if ( empty( $hour['time'] ) ) {
				continue;
			}

			$combined[ trim( $hour['time'] ) ][] = $this->get_localized_day( $hour['day'] );
		}

		return $combined;
	}

	/**
	 * Retrieve the full translated weekday word.
	 *
	 * @param string $day Day to translate.
	 *
	 * @return string
	 */
	private function get_localized_day( $day ) {
		global $wp_locale;

		$hash = [
			'Sunday'    => 0,
			'Monday'    => 1,
			'Tuesday'   => 2,
			'Wednesday' => 3,
			'Thursday'  => 4,
			'Friday'    => 5,
			'Saturday'  => 6,
		];

		return $wp_locale->get_weekday( $hash[ $day ] );
	}

	/**
	 * Output phone numbers.
	 */
	private function display_phone() {
		$phones = Helper::get_settings( 'titles.cpseo_phone_numbers' );
		if ( ! isset( $phones[0]['number'] ) ) {
			return;
		}

		foreach ( $phones as $phone ) :
			$number = esc_html( $phone['number'] );
			?>
			<div class="cpseo-phone-number type-<?php echo sanitize_html_class( $phone['type'] ); ?>">
				<label><?php echo ucwords( $phone['type'] ); ?>:</label> <span><?php echo isset( $phone['number'] ) ? '<a href="tel://' . $number . '">' . $number . '</a>' : ''; ?></span>
			</div>
			<?php
		endforeach;
	}

	/**
	 * Output social identities.
	 */
	private function display_social() {
		$networks = [
			'facebook'      => 'Facebook',
			'twitter'       => 'Twitter',
			'linkedin'      => 'LinkedIn',
			'instagram'     => 'Instagram',
			'youtube'       => 'YouTube',
			'pinterest'     => 'Pinterest',
		];
		?>
		<div class="cpseo-social-networks">
			<?php
			foreach ( $networks as $id => $label ) :
				if ( $url = Helper::get_settings( 'titles.cpseo_social_url_' . $id ) ) : // phpcs:ignore
					?>
					<a class="social-item type-<?php echo $id; ?>" href="<?php echo esc_url( $url ); ?>"><?php echo $label; ?></a>
					<?php
				endif;
			endforeach;
			?>
		</div>
		<?php
	}

	/**
	 * Output name.
	 */
	private function display_name() {
		$name = Helper::get_settings( 'titles.cpseo_knowledgegraph_name' );
		if ( false === $name ) {
			return;
		}

		$url = Helper::get_settings( 'titles.cpseo_url' );
		?>
		<h4 class="cpseo-name">
			<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a>
		</h3>
		<?php
	}

	/**
	 * Output email.
	 */
	private function display_email() {
		$email = Helper::get_settings( 'titles.cpseo_email' );
		if ( false === $email ) {
			return;
		}
		?>
		<div class="cpseo-email">
			<label><?php esc_html_e( 'Email:', 'cpseo' ); ?></label>
			<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
		</div>
		<?php
	}

	/**
	 * Yoast address compatibility functionality.
	 *
	 * @param  array $args Array of arguments.
	 * @return string
	 */
	public function yoast_address( $args ) {
		$atts = shortcode_atts(
			[
				'hide_name'          => '0',
				'hide_address'       => '0',
				'show_state'         => '1',
				'show_country'       => '1',
				'show_phone'         => '1',
				'show_phone_2'       => '1',
				'show_fax'           => '1',
				'show_email'         => '1',
				'show_url'           => '0',
				'show_vat'           => '0',
				'show_tax'           => '0',
				'show_coc'           => '0',
				'show_price_range'   => '0',
				'show_logo'          => '0',
				'show_opening_hours' => '0',
			],
			$args,
			'wpseo_address'
		);
		$show = [ 'address' ];

		if ( 1 === absint( $atts['show_phone'] ) ) {
			$show[] = 'phone';
		}

		if ( 1 === absint( $atts['show_opening_hours'] ) ) {
			$show[] = 'hours';
		}

		return $this->contact_info(
			[
				'show'  => join( ',', $show ),
				'class' => 'wpseo_address_compat',
			]
		);
	}

	/**
	 * Yoast opening hours compatibility functionality.
	 *
	 * @param  array $args Array of arguments.
	 * @return string
	 */
	public function yoast_opening_hours( $args ) {
		return $this->contact_info(
			[
				'show'  => 'hours',
				'class' => 'wpseo_opening_hours_compat',
			]
		);
	}
}
