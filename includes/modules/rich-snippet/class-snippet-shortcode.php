<?php
/**
 * The Rich Snippet Shortcode
 *
 * @since      1.0.24
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet

 */

namespace Classic_SEO\RichSnippet;

use Classic_SEO\Helper;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Traits\Shortcode;
use Classic_SEO\Helpers\Str;
use Classic_SEO\Helpers\Param;

defined( 'ABSPATH' ) || exit;

/**
 * Snippet_Shortcode class.
 */
class Snippet_Shortcode {

	use Hooker, Shortcode;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->add_shortcode( 'cpseo_rich_snippet', 'rich_snippet' );
		$this->add_shortcode( 'cpseo_review_snippet', 'rich_snippet' );

		if ( ! is_admin() ) {
			$this->filter( 'the_content', 'add_review_to_content', 11 );
		}
	}

	/**
	 * Rich Snippet shortcode.
	 *
	 * @param  array $atts Optional. Shortcode arguments - currently only 'show'
	 *                     parameter, which is a comma-separated list of elements to show.
	 *
	 * @return string Shortcode output.
	 */
	public function rich_snippet( $atts ) {
		$atts = shortcode_atts(
			[ 'id' => get_the_ID() ],
			$atts,
			'cpseo_rich_snippet'
		);

		if ( 'edit' === Param::get( 'context' ) ) {
			cpseo()->variables->setup();
		}

		$post = get_post( $atts['id'] );
		if ( empty( $post ) ) {
			return esc_html__( 'Post ID does not exists or was deleted.', 'cpseo' );
		}

		return $this->do_filter( 'snippet/html', $this->get_snippet_content( $post ) );
	}

	/**
	 * Get Snippet content.
	 *
	 * @param WP_Post $post Post Object.
	 *
	 * @return string Shortcode output.
	 */
	public function get_snippet_content( $post ) {
		$schema = Helper::get_post_meta( 'cpseo_rich_snippet', $post->ID );
		if ( ! $this->get_fields( $schema ) ) {
			return __( 'Snippet not selected.', 'cpseo' );
		}

		wp_enqueue_style( 'cpseo-review-snippet', cpseo()->assets() . 'css/cpseo-snippet.css', null, cpseo()->version );

		// Title.
		$title = Helper::get_post_meta( 'cpseo_snippet_name', $post->ID );
		$title = $title ? $title : Helper::replace_vars( '%title%', $post );

		// Description.
		$excerpt = Helper::replace_vars( '%excerpt%', $post );
		$desc    = Helper::get_post_meta( 'cpseo_snippet_desc', $post->ID );
		$desc    = $desc ? $desc : ( $excerpt ? $excerpt : Helper::get_post_meta( 'cpseo_description', $post->ID ) );

		// Image.
		$image = Helper::get_thumbnail_with_fallback( $post->ID, 'medium' );

		ob_start();
		?>
			<div id="cpseo-rich-snippet-wrapper">

				<h5 class="cpseo-title"><?php echo $title; ?></h5>

				<?php if ( ! empty( $image ) ) { ?>
					<div class="cpseo-review-image">
						<img src="<?php echo esc_url( $image[0] ); ?>" />
					</div>
				<?php } ?>

				<div class="cpseo-review-data">
					<p><?php echo do_shortcode( $desc ); ?></p>
					<?php
					foreach ( $this->get_fields( $schema ) as $id => $field ) {
						$this->get_field_content( $id, $field, $post );
					}
					?>
				</div>

			</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Get Field Content.
	 *
	 * @param string  $id    Field ID.
	 * @param string  $field Field Name.
	 * @param WP_Post $post  Post Object.
	 */
	public function get_field_content( $id, $field, $post ) {
		if ( 'is_rating' === $id ) {
			$this->show_ratings( $post->ID, $field );
			return;
		}

		$id = 'event_startdate_date' === $id ? 'event_startdate' : ( 'event__enddate' === $id ? 'event_enddate' : $id );
		if ( ! $value = Helper::get_post_meta( "snippet_{$id}", $post->ID ) ) { // phpcs:ignore
			return;
		}
		?>
		<p>
			<strong><?php echo $field; ?>: </strong>
			<?php
			if ( in_array( $id, [ 'recipe_instructions', 'recipe_ingredients', 'book_editions' ], true ) ) {
				$perform = "get_{$id}";
				$this->$perform( $value );
				return;
			}

			if ( 'jobposting_logo' === $id ) {
				echo '<img src="' . esc_url( $value ) . '" />';
				return;
			}

			$value = in_array( $id, [ 'jobposting_startdate', 'jobposting_expirydate', 'event_startdate', 'event_enddate' ], true ) ? date_i18n( 'Y-m-d H:i', $value ) : $value;

			echo is_array( $value ) ? implode( ', ', $value ) : esc_html( $value );
			?>
		</p>
		<?php
	}

	/**
	 * Get Recipe Instructions.
	 *
	 * @param array $value Recipe instructions.
	 */
	public function get_recipe_instructions( $value ) {
		foreach ( $value as $key => $data ) {
			echo '<p><strong>' . $data['name'] . ': </strong>' . $data['text'] . '</p>';
		}
	}

	/**
	 * Get Recipe Ingredients.
	 *
	 * @param string $value Recipe ingredients.
	 */
	public function get_recipe_ingredients( $value ) {
		if ( Str::contains( "\r\n", $value ) ) {
			echo '<ul>';
			echo '<li>' . str_replace( "\r\n", "</li>\n<li>", $value ) . '</li>';
			echo '</ul>';
			return;
		}

		echo $value;
	}

	/**
	 * Get Book Editions.
	 *
	 * @param array $value Book editions.
	 */
	public function get_book_editions( $value ) {
		$hash = [
			'book_edition'   => __( 'Edition', 'cpseo' ),
			'name'           => __( 'Name', 'cpseo' ),
			'author'         => __( 'Author', 'cpseo' ),
			'isbn'           => __( 'ISBN', 'cpseo' ),
			'date_published' => __( 'Date Published', 'cpseo' ),
			'book_format'    => __( 'Format', 'cpseo' ),
		];
		foreach ( $value as $data ) {
			echo '<p>';
			foreach ( $hash as $id => $field ) {
				echo isset( $data[ $id ] ) ? "<strong>{$field} : </strong> {$data[ $id ]} <br />" : '';
			}
			echo '</p>';
		}

	}

	/**
	 * Display nicely formatted reviews.
	 *
	 * @param int   $post_id The Post ID.
	 * @param array $field   Array of review value and count field.
	 */
	public function show_ratings( $post_id, $field ) {
		wp_enqueue_style( 'font-awesome', cpseo()->plugin_url() . 'assets/vendor/font-awesome/css/font-awesome.min.css', null, '4.7.0' );
		$rating = isset( $field['value'] ) ? (float) Helper::get_post_meta( "snippet_{$field['value']}", $post_id ) : '';
		$count  = isset( $field['count'] ) ? Helper::get_post_meta( $field['count'], $post_id ) : '';
		?>
		<div class="cpseo-total-wrapper">

			<strong><?php echo $this->do_filter( 'review/text', esc_html__( 'Editor\'s Rating:', 'cpseo' ) ); ?></strong><br />

			<span class="cpseo-total"><?php echo $rating; ?></span>

			<div class="cpseo-review-star">

				<div class="cpseo-review-result-wrapper">

					<?php echo \str_repeat( '<i class="fa fa-star"></i>', 5 ); ?>

					<div class="cpseo-review-result" style="width:<?php echo ( $rating * 20 ); ?>%;">
						<?php echo \str_repeat( '<i class="fa fa-star"></i>', 5 ); ?>
					</div>

				</div>

			</div>

		</div>
		<?php
	}

	/**
	 * Contact info shortcode, displays nicely formatted contact informations.
	 *
	 * @param string $type Snippet type.
	 *
	 * @return array Array of snippet fields.
	 */
	public function get_fields( $type ) {
		$fields = [
			'course'     => [
				'course_provider_type' => esc_html__( 'Course Provider', 'cpseo' ),
				'course_provider'      => esc_html__( 'Course Provider Name', 'cpseo' ),
				'course_provider_url'  => esc_html__( 'Course Provider URL', 'cpseo' ),
				'is_rating'            => [
					'value' => 'course_rating',
				],
			],
			'event'      => [
				'url'                            => esc_html__( 'URL', 'cpseo' ),
				'event_type'                     => esc_html__( 'Event Type', 'cpseo' ),
				'event_venue'                    => esc_html__( 'Venue Name', 'cpseo' ),
				'event_venue_url'                => esc_html__( 'Venue URL', 'cpseo' ),
				'event_address'                  => esc_html__( 'Address', 'cpseo' ),
				'event_performer_type'           => esc_html__( 'Performer', 'cpseo' ),
				'event_performer'                => esc_html__( 'Performer Name', 'cpseo' ),
				'event_performer_url'            => esc_html__( 'Performer URL', 'cpseo' ),
				'event_status'                   => esc_html__( 'Event Status', 'cpseo' ),
				'event_startdate_date'           => esc_html__( 'Start Date', 'cpseo' ),
				'event__enddate'                 => esc_html__( 'End Date', 'cpseo' ),
				'event_ticketurl'                => esc_html__( 'Ticket URL', 'cpseo' ),
				'event_price'                    => esc_html__( 'Entry Price', 'cpseo' ),
				'event_currency'                 => esc_html__( 'Currency', 'cpseo' ),
				'event_availability'             => esc_html__( 'Availability', 'cpseo' ),
				'event_availability_starts_date' => esc_html__( 'Availability Starts', 'cpseo' ),
				'event_inventory'                => esc_html__( 'Stock Inventory', 'cpseo' ),
				'is_rating'                      => [
					'value' => 'event_rating',
				],
			],
			'jobposting' => [
				'jobposting_salary'          => esc_html__( 'Salary', 'cpseo' ),
				'jobposting_currency'        => esc_html__( 'Salary Currency', 'cpseo' ),
				'jobposting_payroll'         => esc_html__( 'Payroll', 'cpseo' ),
				'jobposting_startdate'       => esc_html__( 'Date Posted', 'cpseo' ),
				'jobposting_expirydate'      => esc_html__( 'Expiry Posted', 'cpseo' ),
				'jobposting_employment_type' => esc_html__( 'Employment Type ', 'cpseo' ),
				'jobposting_organization'    => esc_html__( 'Hiring Organization ', 'cpseo' ),
				'jobposting_id'              => esc_html__( 'Posting ID', 'cpseo' ),
				'jobposting_url'             => esc_html__( 'Organization URL', 'cpseo' ),
				'jobposting_logo'            => esc_html__( 'Organization Logo', 'cpseo' ),
				'jobposting_address'         => esc_html__( 'Location', 'cpseo' ),
			],
			'music'      => [
				'url'        => esc_html__( 'URL', 'cpseo' ),
				'music_type' => esc_html__( 'Type', 'cpseo' ),
			],
			'product'    => [
				'product_sku'         => esc_html__( 'Product SKU', 'cpseo' ),
				'product_brand'       => esc_html__( 'Product Brand', 'cpseo' ),
				'product_currency'    => esc_html__( 'Product Currency', 'cpseo' ),
				'product_price'       => esc_html__( 'Product Price', 'cpseo' ),
				'product_price_valid' => esc_html__( 'Price Valid Until', 'cpseo' ),
				'product_instock'     => esc_html__( 'Product In-Stock', 'cpseo' ),
				'is_rating'           => [
					'value' => 'product_rating',
				],
			],
			'recipe'     => [
				'recipe_type'                => esc_html__( 'Type', 'cpseo' ),
				'recipe_cuisine'             => esc_html__( 'Cuisine', 'cpseo' ),
				'recipe_keywords'            => esc_html__( 'Keywords', 'cpseo' ),
				'recipe_yield'               => esc_html__( 'Recipe Yield', 'cpseo' ),
				'recipe_calories'            => esc_html__( 'Calories', 'cpseo' ),
				'recipe_preptime'            => esc_html__( 'Preparation Time', 'cpseo' ),
				'recipe_cooktime'            => esc_html__( 'Cooking Time', 'cpseo' ),
				'recipe_totaltime'           => esc_html__( 'Total Time', 'cpseo' ),
				'recipe_video'               => esc_html__( 'Recipe Video', 'cpseo' ),
				'recipe_video_thumbnail'     => esc_html__( 'Recipe Video Thumbnail', 'cpseo' ),
				'recipe_video_name'          => esc_html__( 'Recipe Video Name', 'cpseo' ),
				'recipe_video_date'          => esc_html__( 'Video Upload Date', 'cpseo' ),
				'recipe_video_description'   => esc_html__( 'Recipe Video Description', 'cpseo' ),
				'recipe_ingredients'         => esc_html__( 'Recipe Ingredients', 'cpseo' ),
				'recipe_instruction_name'    => esc_html__( 'Recipe Instruction Name', 'cpseo' ),
				'recipe_single_instructions' => esc_html__( 'Recipe Instructions', 'cpseo' ),
				'recipe_instructions'        => esc_html__( 'Recipe Instructions', 'cpseo' ),
				'is_rating'                  => [
					'value' => 'recipe_rating',
				],
			],
			'restaurant' => [
				'local_address'             => esc_html__( 'Address', 'cpseo' ),
				'local_phone'               => esc_html__( 'Phone Number', 'cpseo' ),
				'local_price_range'         => esc_html__( 'Price Range', 'cpseo' ),
				'local_opens'               => esc_html__( 'Opening Time', 'cpseo' ),
				'local_closes'              => esc_html__( 'Closing Time', 'cpseo' ),
				'local_opendays'            => esc_html__( 'Open Days', 'cpseo' ),
				'restaurant_serves_cuisine' => esc_html__( 'Serves Cuisine', 'cpseo' ),
				'restaurant_menu'           => esc_html__( 'Menu URL', 'cpseo' ),
			],
			'video'      => [
				'video_url'       => esc_html__( 'Content URL', 'cpseo' ),
				'video_embed_url' => esc_html__( 'Embed URL', 'cpseo' ),
				'video_duration'  => esc_html__( 'Duration', 'cpseo' ),
				'video_views'     => esc_html__( 'Views', 'cpseo' ),
			],
			'person'     => [
				'person_email'     => esc_html__( 'Email', 'cpseo' ),
				'person_address'   => esc_html__( 'Address', 'cpseo' ),
				'person_gender'    => esc_html__( 'Gender', 'cpseo' ),
				'person_job_title' => esc_html__( 'Job Title', 'cpseo' ),
			],
			'review'     => [
				'is_rating' => [
					'value' => 'review_rating_value',
				],
			],
			'service'    => [
				'service_type'           => esc_html__( 'Service Type', 'cpseo' ),
				'service_price'          => esc_html__( 'Price', 'cpseo' ),
				'service_price_currency' => esc_html__( 'Currency', 'cpseo' ),
			],
			'software'   => [
				'software_price'                => esc_html__( 'Price', 'cpseo' ),
				'software_price_currency'       => esc_html__( 'Price Currency', 'cpseo' ),
				'software_operating_system'     => esc_html__( 'Operating System', 'cpseo' ),
				'software_application_category' => esc_html__( 'Application Category', 'cpseo' ),
				'is_rating'                     => [
					'value' => 'software_rating',
				],
			],
			'book'       => [
				'url'           => esc_html__( 'URL', 'cpseo' ),
				'author'        => esc_html__( 'Author', 'cpseo' ),
				'book_editions' => esc_html__( 'Book Editions', 'cpseo' ),
				'is_rating'     => [
					'value' => 'book_rating',
				],
			],
		];

		return isset( $fields[ $type ] ) ? apply_filters( 'cpseo/snippet/fields', $fields[ $type ] ) : false;
	}


	/**
	 * Injects reviews to content.
	 *
	 * @param  string $content Post content.
	 * @return string
	 *
	 * @since 1.0.12
	 */
	public function add_review_to_content( $content ) {
		$location = $this->get_content_location();
		if ( false === $location ) {
			return $content;
		}

		$review = do_shortcode( '[cpseo_review_snippet]' );

		if ( in_array( $location, [ 'top', 'both' ], true ) ) {
			$content = $review . $content;
		}

		if ( in_array( $location, [ 'bottom', 'both' ], true ) && $this->can_add_multi_page() ) {
			$content .= $review;
		}

		return $content;
	}

	/**
	 * Check if we can inject the review in the content.
	 *
	 * @return boolean|string
	 */
	private function get_content_location() {
		/**
		 * Filter: Allow disabling the review display.
		 *
		 * @param bool $return True to disable.
		 */
		if ( ! is_main_query() || ! in_the_loop() || $this->do_filter( 'snippet/review/hide_data', false ) ) {
			return false;
		}

		$schema = Helper::get_post_meta( 'rich_snippet' );
		if ( ! in_array( $schema, [ 'book', 'review', 'course', 'event', 'product', 'recipe', 'software' ], true ) ) {
			return false;
		}

		$key      = 'review' === $schema ? 'snippet_review_location' : 'snippet_location';
		$location = $this->do_filter( 'snippet/review/location', Helper::get_post_meta( $key ) );
		$location = $location ? $location : 'custom';

		return 'custom' === $location ? false : $location;
	}

	/**
	 * Check if we can add content if multipage.
	 *
	 * @return bool
	 */
	private function can_add_multi_page() {
		global $multipage, $numpages, $page;

		return ( ! $multipage || $page === $numpages );
	}
}
