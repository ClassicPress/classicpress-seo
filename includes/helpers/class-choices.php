<?php
/**
 * The Choices helpers.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Helpers
 */

namespace Classic_SEO\Helpers;

use Classic_SEO\Helper;
use Classic_SEO\Admin\Admin_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Choices class.
 */
trait Choices {

	/**
	 * Gets list of overlay images for the social thumbnail.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param  string $output Output type.
	 * @return array
	 */
	public static function choices_overlay_images( $output = 'object' ) {
		$uri = CPSEO_PLUGIN_URL . 'assets/admin/img/';
		$dir = CPSEO_PATH . 'assets/admin/img/';

		/**
		 * Allow developers to add/remove overlay images.
		 *
		 * @param array $images Image data as array of arrays.
		 */
		$list = apply_filters(
			'cpseo/social/overlay_images',
			[
				'play' => [
					'name' => esc_html__( 'Play icon', 'cpseo' ),
					'url'  => $uri . 'icon-play.png',
					'path' => $dir . 'icon-play.png',
				],
				'gif'  => [
					'name' => esc_html__( 'GIF icon', 'cpseo' ),
					'url'  => $uri . 'icon-gif.png',
					'path' => $dir . 'icon-gif.png',
				],
			]
		);

		return 'names' === $output ? wp_list_pluck( $list, 'name' ) : $list;
	}

	/**
	 * Get robot choices.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array
	 */
	public static function choices_robots() {
		return [
			'noindex'      => esc_html__( 'No Index', 'cpseo' ) . Admin_Helper::get_tooltip( esc_html__( 'Prevents pages from being indexed and displayed in search engine result pages', 'cpseo' ) ),
			'nofollow'     => esc_html__( 'No Follow', 'cpseo' ) . Admin_Helper::get_tooltip( esc_html__( 'Prevents search engines from following links on the pages', 'cpseo' ) ),
			'noarchive'    => esc_html__( 'No Archive', 'cpseo' ) . Admin_Helper::get_tooltip( esc_html__( 'Prevents search engines from showing Cached links for pages', 'cpseo' ) ),
			'noimageindex' => esc_html__( 'No Image Index', 'cpseo' ) . Admin_Helper::get_tooltip( esc_html__( 'Lets you specify that you do not want your pages to appear as the referring page for images that appear in image search results', 'cpseo' ) ),
			'nosnippet'    => esc_html__( 'No Snippet', 'cpseo' ) . Admin_Helper::get_tooltip( esc_html__( 'Prevents a snippet from being shown in the search results', 'cpseo' ) ),
		];
	}

	/**
	 * Get separator choices.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param  string $current Currently saved separator if any.
	 * @return array
	 */
	public static function choices_separator( $current = '' ) {
		$defaults = [ '-', '&ndash;', '&mdash;', '&laquo;', '&raquo;', '|', '&bull;', '&middot;', '*', '&#8902;', '&lt;', '&gt;' ];
		if ( ! $current || in_array( $current, $defaults, true ) ) {
			$current = '';
		}

		return [
			'-'			=> '-',
			'&ndash;'	=> '&ndash;',
			'&mdash;'	=> '&mdash;',
			'&laquo;'	=> '&laquo;',
			'&raquo;'	=> '&raquo;',
			'|'			=> '|',
			'&bull;'	=> '&bull;',
			'&middot;'	=> '&middot;',
			'*'			=> '*',
			'&#8902;'	=> '&#8902;',
			'&lt;'		=> '&lt;',
			'&gt;'		=> '&gt;',
		];
	}

	/**
	 * Get all accessible post types as choices.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array
	 */
	public static function choices_post_types() {
		static $choices_post_types;

		if ( ! isset( $choices_post_types ) ) {
			$choices_post_types = Helper::get_accessible_post_types();
			$choices_post_types = \array_map( function( $post_type ) {
				$object = get_post_type_object( $post_type );
				return $object->label;
			}, $choices_post_types );
		}

		return $choices_post_types;
	}

	/**
	 * Get all post types.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array
	 */
	public static function choices_any_post_types() {

		$post_types = self::choices_post_types();
		unset( $post_types['attachment'] );

		return [ 'any' => esc_html__( 'Any', 'cpseo' ) ] + $post_types + [ 'comments' => esc_html( translate( 'Comments' ) ) ]; // phpcs:ignore
	}

	/**
	 * Get business types as choices.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param  bool $none Add none option to list.
	 * @return array
	 */
	public static function choices_business_types( $none = false ) {
		$data = apply_filters(
			'cpseo/json_ld/business_types',
			[
				[ 'label' => 'Airport' ],
				[ 'label' => 'Animal Shelter' ],
				[ 'label' => 'Aquarium' ],
				[
					'label' => 'Automotive Business',
					'child' => [
						[ 'label' => 'Auto Body Shop' ],
						[ 'label' => 'Auto Dealer' ],
						[ 'label' => 'Auto Parts Store' ],
						[ 'label' => 'Auto Rental' ],
						[ 'label' => 'Auto Repair' ],
						[ 'label' => 'Auto Wash' ],
						[ 'label' => 'Gas Station' ],
						[ 'label' => 'Motorcycle Dealer' ],
						[ 'label' => 'Motorcycle Repair' ],
					],
				],
				[ 'label' => 'Beach' ],
				[ 'label' => 'Bus Station' ],
				[ 'label' => 'BusStop' ],
				[ 'label' => 'Campground' ],
				[ 'label' => 'Cemetery' ],
				[ 'label' => 'Child Care' ],
				[ 'label' => 'Corporation' ],
				[ 'label' => 'Crematorium' ],
				[ 'label' => 'Dry Cleaning or Laundry' ],
				[
					'label' => 'Educational Organization',
					'child' => [
						[ 'label' => 'College or University' ],
						[ 'label' => 'Elementary School' ],
						[ 'label' => 'High School' ],
						[ 'label' => 'Middle School' ],
						[ 'label' => 'Preschool' ],
						[ 'label' => 'School' ],
					],
				],
				[
					'label' => 'Emergency Service',
					'child' => [
						[ 'label' => 'Fire Station' ],
						[ 'label' => 'Hospital' ],
						[ 'label' => 'Police Station' ],
					],
				],
				[ 'label' => 'Employment Agency' ],
				[
					'label' => 'Entertainment Business',
					'child' => [
						[ 'label' => 'Adult Entertainment' ],
						[ 'label' => 'Amusement Park' ],
						[ 'label' => 'Art Gallery' ],
						[ 'label' => 'Casino' ],
						[ 'label' => 'Comedy Club' ],
						[ 'label' => 'Movie Theater' ],
						[ 'label' => 'Night Club' ],
					],
				],
				[ 'label' => 'Event Venue' ],
				[
					'label' => 'Financial Service',
					'child' => [
						[ 'label' => 'Accounting Service' ],
						[ 'label' => 'Automated Teller' ],
						[ 'label' => 'Bank or Credit Union' ],
						[ 'label' => 'Insurance Agency' ],
					],
				],
				[ 'label' => 'Fire Station' ],
				[
					'label' => 'Food Establishment',
					'child' => [
						[ 'label' => 'Bakery' ],
						[ 'label' => 'Bar or Pub' ],
						[ 'label' => 'Brewery' ],
						[ 'label' => 'Cafe or Coffee Shop' ],
						[ 'label' => 'Fast Food Restaurant' ],
						[ 'label' => 'Ice Cream Shop' ],
						[ 'label' => 'Restaurant' ],
						[ 'label' => 'Winery' ],
					],
				],
				[
					'label' => 'Government Building',
					'child' => [
						[ 'label' => 'City Hall' ],
						[ 'label' => 'Courthouse' ],
						[ 'label' => 'Defence Establishment' ],
						[ 'label' => 'Embassy' ],
						[ 'label' => 'Legislative Building' ],
					],
				],
				[
					'label' => 'Government Office',
					'child' => [
						[ 'label' => 'Post Office' ],
					],
				],
				[ 'label' => 'Government Organization' ],
				[
					'label' => 'Health And Beauty Business',
					'child' => [
						[ 'label' => 'Beauty Salon' ],
						[ 'label' => 'Day Spa' ],
						[ 'label' => 'Hair Salon' ],
						[ 'label' => 'Health Club' ],
						[ 'label' => 'Nail Salon' ],
						[ 'label' => 'Tattoo Parlor' ],
					],
				],
				[
					'label' => 'Home And Construction Business',
					'child' => [
						[ 'label' => 'Electrician' ],
						[ 'label' => 'General Contractor' ],
						[ 'label' => 'HVAC Business' ],
						[ 'label' => 'House Painter' ],
						[ 'label' => 'Locksmith' ],
						[ 'label' => 'Moving Company' ],
						[ 'label' => 'Plumber' ],
						[ 'label' => 'Roofing Contractor' ],
					],
				],
				[ 'label' => 'Hospital' ],
				[ 'label' => 'Internet Cafe' ],
				[ 'label' => 'Library' ],
				[ 'label' => 'Local Business' ],
				[
					'label' => 'Lodging Business',
					'child' => [
						[ 'label' => 'Bed And Breakfast' ],
						[ 'label' => 'Hostel' ],
						[ 'label' => 'Hotel' ],
						[ 'label' => 'Motel' ],
					],
				],
				[
					'label' => 'Medical Organization',
					'child' => [
						[ 'label' => 'Dentist' ],
						[ 'label' => 'Diagnostic Lab' ],
						[ 'label' => 'Hospital' ],
						[ 'label' => 'Medical Clinic' ],
						[ 'label' => 'Optician' ],
						[ 'label' => 'Pharmacy' ],
						[ 'label' => 'Physician' ],
						[ 'label' => 'Veterinary Care' ],
					],
				],
				[ 'label' => 'Movie Theater' ],
				[ 'label' => 'Museum' ],
				[ 'label' => 'Music Venue' ],
				[ 'label' => 'NGO' ],
				[ 'label' => 'Organization' ],
				[ 'label' => 'Park' ],
				[ 'label' => 'Parking Facility' ],
				[ 'label' => 'Performing Arts Theater' ],
				[
					'label' => 'Performing Group',
					'child' => [
						[ 'label' => 'Dance Group' ],
						[ 'label' => 'Music Group' ],
						[ 'label' => 'Theater Group' ],
					],
				],
				[
					'label' => 'Place Of Worship',
					'child' => [
						[ 'label' => 'Buddhist Temple' ],
						[ 'label' => 'Catholic Church' ],
						[ 'label' => 'Church' ],
						[ 'label' => 'Hindu Temple' ],
						[ 'label' => 'Mosque' ],
						[ 'label' => 'Synagogue' ],
					],
				],
				[ 'label' => 'Playground' ],
				[ 'label' => 'PoliceStation' ],
				[
					'label' => 'Professional Service',
					'child' => [
						[ 'label' => 'Accounting Service' ],
						[ 'label' => 'Legal Service' ],
						[ 'label' => 'Dentist' ],
						[ 'label' => 'Electrician' ],
						[ 'label' => 'General Contractor' ],
						[ 'label' => 'House Painter' ],
						[ 'label' => 'Locksmith' ],
						[ 'label' => 'Notary' ],
						[ 'label' => 'Plumber' ],
						[ 'label' => 'Roofing Contractor' ],
					],
				],
				[ 'label' => 'Radio Station' ],
				[ 'label' => 'Real Estate Agent' ],
				[ 'label' => 'Recycling Center' ],
				[
					'label' => 'Residence',
					'child' => [
						[ 'label' => 'Apartment Complex' ],
						[ 'label' => 'Gated Residence Community' ],
						[ 'label' => 'Single Family Residence' ],
					],
				],
				[ 'label' => 'RV Park' ],
				[ 'label' => 'Self Storage' ],
				[ 'label' => 'Shopping Center' ],
				[
					'label' => 'Sports Activity Location',
					'child' => [
						[ 'label' => 'Bowling Alley' ],
						[ 'label' => 'Exercise Gym' ],
						[ 'label' => 'Golf Course' ],
						[ 'label' => 'Health Club' ],
						[ 'label' => 'Public Swimming Pool' ],
						[ 'label' => 'Ski Resort' ],
						[ 'label' => 'Sports Club' ],
						[ 'label' => 'Stadium or Arena' ],
						[ 'label' => 'Tennis Complex' ],
					],
				],
				[ 'label' => 'Sports Team' ],
				[ 'label' => 'Stadium Or Arena' ],
				[
					'label' => 'Store',
					'child' => [
						[ 'label' => 'Auto Parts Store' ],
						[ 'label' => 'Bike Store' ],
						[ 'label' => 'Book Store' ],
						[ 'label' => 'Clothing Store' ],
						[ 'label' => 'Computer Store' ],
						[ 'label' => 'Convenience Store' ],
						[ 'label' => 'Department Store' ],
						[ 'label' => 'Electronics Store' ],
						[ 'label' => 'Florist' ],
						[ 'label' => 'Furniture Store' ],
						[ 'label' => 'Garden Store' ],
						[ 'label' => 'Grocery Store' ],
						[ 'label' => 'Hardware Store' ],
						[ 'label' => 'Hobby Shop' ],
						[ 'label' => 'HomeGoods Store' ],
						[ 'label' => 'Jewelry Store' ],
						[ 'label' => 'Liquor Store' ],
						[ 'label' => 'Mens Clothing Store' ],
						[ 'label' => 'Mobile Phone Store' ],
						[ 'label' => 'Movie Rental Store' ],
						[ 'label' => 'Music Store' ],
						[ 'label' => 'Office Equipment Store' ],
						[ 'label' => 'Outlet Store' ],
						[ 'label' => 'Pawn Shop' ],
						[ 'label' => 'Pet Store' ],
						[ 'label' => 'Shoe Store' ],
						[ 'label' => 'Sporting Goods Store' ],
						[ 'label' => 'Tire Shop' ],
						[ 'label' => 'Toy Store' ],
						[ 'label' => 'Wholesale Store' ],
					],
				],
				[ 'label' => 'Subway Station' ],
				[ 'label' => 'Television Station' ],
				[ 'label' => 'Tourist Information Center' ],
				[ 'label' => 'Train Station' ],
				[ 'label' => 'Travel Agency' ],
				[ 'label' => 'Taxi Stand' ],
				[ 'label' => 'Website' ],
				[ 'label' => 'Graphic Novel' ],
				[ 'label' => 'Zoo' ],
			]
		);

		$business = [];
		if ( $none ) {
			$business['off'] = 'None';
		}

		foreach ( $data as $item ) {
			$business[ str_replace( ' ', '', $item['label'] ) ] = $item['label'];

			if ( isset( $item['child'] ) ) {
				foreach ( $item['child'] as $child ) {
					$business[ str_replace( ' ', '', $child['label'] ) ] = '&mdash; ' . $child['label'];
				}
			}
		}

		return $business;
	}

	/**
	 * Get Rich Snippet types as choices.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param  bool $none Add none option to the list.
	 * @return array
	 */
	public static function choices_rich_snippet_types( $none = false ) {
		$types = [
			'article'    => esc_html__( 'Article', 'cpseo' ),
			'book'       => esc_html__( 'Book', 'cpseo' ),
			'course'     => esc_html__( 'Course', 'cpseo' ),
			'event'      => esc_html__( 'Event', 'cpseo' ),
			'jobposting' => esc_html__( 'Job Posting', 'cpseo' ),
			'music'      => esc_html__( 'Music', 'cpseo' ),
			'product'    => esc_html__( 'Product', 'cpseo' ),
			'recipe'     => esc_html__( 'Recipe', 'cpseo' ),
			'restaurant' => esc_html__( 'Restaurant', 'cpseo' ),
			'video'      => esc_html__( 'Video', 'cpseo' ),
			'person'     => esc_html__( 'Person', 'cpseo' ),
			'service'    => esc_html__( 'Service', 'cpseo' ),
			'software'   => esc_html__( 'Software Application', 'cpseo' ),
		];
		
		if ( ! empty( self::get_review_posts() ) ) {
			$types['review'] = esc_html__( 'Review', 'cpseo' );
		}

		if ( is_string( $none ) ) {
			$types = [ 'off' => $none ] + $types;
		}

		/**
		 * Allow developers to add/remove Rich Snippet type choices.
		 *
		 * @param array $types Rich Snippet types.
		 */
		return apply_filters( 'cpseo/settings/snippet/types', $types );
	}

	/**
	 * Get the redirection types.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array
	 */
	public static function choices_redirection_types() {
		return [
			'301' => esc_html__( '301 Permanent Move', 'cpseo' ),
			'302' => esc_html__( '302 Temporary Move', 'cpseo' ),
			'307' => esc_html__( '307 Temporary Redirect', 'cpseo' ),
			'410' => esc_html__( '410 Content Deleted', 'cpseo' ),
			'451' => esc_html__( '451 Content Unavailable for Legal Reasons', 'cpseo' ),
		];
	}

	/**
	 * Get comparison types.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array
	 */
	public static function choices_comparison_types() {
		return [
			'exact'    => esc_html__( 'Exact', 'cpseo' ),
			'contains' => esc_html__( 'Contains', 'cpseo' ),
			'start'    => esc_html__( 'Starts With', 'cpseo' ),
			'end'      => esc_html__( 'End With', 'cpseo' ),
			'regex'    => esc_html__( 'Regex', 'cpseo' ),
		];
	}

	/**
	 * Get Post type icons.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array
	 */
	public static function choices_post_type_icons() {
		/**
		 * Allow developer to change post types icons.
		 *
		 * @param array $icons Array of available icons.
		 */
		return apply_filters(
			'cpseo/post_type_icons',
			[
				'default'    => 'dashicons dashicons-admin-post',
				'post'       => 'dashicons dashicons-admin-post',
				'page'       => 'dashicons dashicons-admin-page',
				'attachment' => 'dashicons dashicons-admin-media',
				'product'    => 'fa fa-shopping-cart',
			]
		);
	}

	/**
	 * Get Taxonomy icons.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array
	 */
	public static function choices_taxonomy_icons() {
		/**
		 * Allow developer to change taxonomies icons.
		 *
		 * @param array $icons Array of available icons.
		 */
		return apply_filters(
			'cpseo/taxonomy_icons',
			[
				'default'     => 'dashicons dashicons-tag',
				'category'    => 'dashicons dashicons-category',
				'post_tag'    => 'dashicons dashicons-tag',
				'product_cat' => 'dashicons dashicons-category',
				'product_tag' => 'dashicons dashicons-tag',
				'post_format' => 'dashicons dashicons-format-image',
			]
		);
	}
	
	/**
	 * Function to get posts having review schema type selected.
	 */
	public static function get_review_posts() {
		static $posts = null;
		if ( null === $posts ) {
			global $wpdb;

			$meta_query = new \WP_Meta_Query([
				'relation' => 'AND',
				[
					'key'   => 'cpseo_rich_snippet',
					'value' => 'review',
				],
			]);

			$meta_query = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$posts = $wpdb->get_col( "SELECT {$wpdb->posts}.ID FROM $wpdb->posts {$meta_query['join']} WHERE 1=1 {$meta_query['where']} AND ({$wpdb->posts}.post_status = 'publish')" ); // phpcs:ignore
		}

		return $posts;
	}
}
