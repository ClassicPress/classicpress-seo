<?php
/**
 * The SERP preview functionality, in the metabox.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin
 */

namespace Classic_SEO\Admin;

use Classic_SEO\CMB2;
use Classic_SEO\Helper;
use Classic_SEO\Rewrite;
use Classic_SEO\Admin\Param;
use Classic_SEO\Helpers\WordPress;
use Classic_SEO\Helpers\Conditional;

defined( 'ABSPATH' ) || exit;

/**
 * Serp_Preview class.
 */
class Serp_Preview {

	use WordPress;

	/**
	 * Display SERP preview.
	 */
	public function display() {
		$method = 'get_' . CMB2::current_object_type() . '_data';
		$data   = $this->$method();
		if ( 'post' === CMB2::current_object_type() && Helper::is_module_active( 'rich-snippet' ) ) {
			$snippet_preview = $this->get_snippet_html();
		}
		$snippet_type    = isset( $snippet_preview['type'] ) ? $snippet_preview['type'] : '';
		$desktop_preview = isset( $snippet_preview['desktop'] ) ? $snippet_preview['desktop'] : '';
		$mobile_preview  = isset( $snippet_preview['mobile'] ) ? $snippet_preview['mobile'] : '';
		?>
		<div class="serp-preview desktop-preview">

			<div class="serp-preview-title" data-title="<?php esc_attr_e( 'Preview', 'cpseo' ); ?>" data-desktop="<?php esc_attr_e( 'Desktop Preview', 'cpseo' ); ?>" data-mobile="<?php esc_attr_e( 'Mobile Preview', 'cpseo' ); ?>">
				<div class="alignleftt clearfix">
					<a href="#" class="button button-secondary cpseo-select-device device-desktop" data-device="desktop"><span class="dashicons dashicons-desktop"></span></a>
					<a href="#" class="button button-secondary cpseo-select-device device-mobile" data-device="mobile"><span class="dashicons dashicons-smartphone"></span></a>
				</div>
			</div>

			<div class="serp-preview-wrapper">

				<div class="serp-preview-body">

					<h5 class="serp-title" data-format="<?php echo esc_attr( $data['title_format'] ); ?>" data-empty-title="<?php esc_attr_e( 'Click to enter custom title', 'cpseo' ); ?>"></h5>

					<span class="serp-url" data-baseurl="<?php echo trailingslashit( substr( $data['url'], 0, strrpos( $data['url'], '/' ) ) ); ?>" data-format="<?php echo esc_attr( $data['permalink_format'] ); ?>" data-empty-title="<?php esc_attr_e( 'Click to enter permalink', 'cpseo' ); ?>"><?php echo esc_url( $data['permalink'] ); ?></span>
					<?php
					if ( 'event' !== $snippet_type ) {
						echo $desktop_preview;
					}
					?>

					<p class="serp-description" data-format="<?php echo esc_attr( $data['desc_format'] ); ?>" data-empty-title="<?php esc_attr_e( 'Click to enter custom meta description', 'cpseo' ); ?>"></p>

					<?php
					if ( 'event' === $snippet_type ) {
						echo $desktop_preview;
					}

					echo $mobile_preview;
					?>

				</div>

				<div class="serp-preview-noindex">
					<h3><?php esc_html_e( 'Noindex robots meta is enabled', 'cpseo' ); ?></h3>
					<p><?php esc_html_e( 'This page will not appear in search results. You can disable noindex in the Advanced tab.', 'cpseo' ); ?></p>
				</div>

				<div class="serp-preview-footer wp-clearfix">

					<div class="cpseo-ui">
						<a href="#" class="button button-secondary cpseo-edit-snippet"><span class="dashicons dashicons-edit"></span><?php esc_html_e( 'Edit Snippet', 'cpseo' ); ?></a>
						<a href="#" class="button button-secondary cpseo-edit-snippet hidden"><span class="dashicons dashicons-no-alt"></span> <?php esc_html_e( 'Close Editor', 'cpseo' ); ?></a>
					</div>

				</div>

			</div>
		</div>
		<?php
	}

	/**
	 * Get post data for SERP preview.
	 *
	 * @return array
	 */
	private function get_post_data() {
		global $post;
		setup_postdata( $post );

		$post_type    = Serp_Preview::get_post_type();
		$title_format = Helper::get_settings( "titles.cpseo_pt_{$post_type}_title" );
		$desc_format  = Helper::get_settings( "titles.cpseo_pt_{$post_type}_description" );
		$title_format = $title_format ? $title_format : '%title%';

		// Get the permalink.
		list( $permalink_format ) = get_sample_permalink( $post->ID, null, null );

		$permalink = $permalink_format;
		if ( 'publish' === $post->post_status || 'attachment' === $post->post_type ) {
			$permalink = get_permalink( $post );
		} elseif ( 'auto-draft' === $post->post_status && 'post' === $post->post_type ) {
			$post_temp              = $post;
			$post_temp->post_status = 'publish';
			$permalink              = get_permalink( $post_temp, true );
			$permalink_format       = $permalink;
		} else {
			$permalink = str_replace( [ '%pagename%', '%postname%' ], ( $post->post_name ? $post->post_name : sanitize_title( $post->post_title ) ), $permalink_format );
		}

		$url = untrailingslashit( esc_url( $permalink ) );

		return compact( 'title_format', 'desc_format', 'url', 'permalink', 'permalink_format' );
	}

	/**
	 * Get term data for SERP preview.
	 *
	 * @return array
	 */
	private function get_term_data() {
		global $taxnow, $wp_rewrite;

		$term_id  = Param::request( 'tag_ID', 0, FILTER_VALIDATE_INT );
		$term     = get_term( $term_id, $taxnow, OBJECT, 'edit' );
		$taxonomy = get_taxonomy( $term->taxonomy );

		$title_format = Helper::get_settings( "titles.cpseo_tax_{$term->taxonomy}_title" );
		$desc_format  = Helper::get_settings( "titles.cpseo_tax_{$term->taxonomy}_description" );
		$title_format = $title_format ? $title_format : '%term%';

		// Get the permalink.
		$permalink = untrailingslashit( esc_url( get_term_link( $term_id, $term->taxonomy ) ) );
		$termlink  = $wp_rewrite->get_extra_permastruct( $term->taxonomy );

		// Pretty permalinks disabled.
		if ( empty( $termlink ) ) {
			$permalink_format = $permalink;
		} else {
			$slugs            = $this->get_ancestors( $term_id, $term->taxonomy );
			$termlink         = $this->get_termlink( $termlink, $term->taxonomy );
			$slugs[]          = '%postname%';
			$termlink         = str_replace( "%$term->taxonomy%", implode( '/', $slugs ), $termlink );
			$permalink_format = home_url( user_trailingslashit( $termlink, 'category' ) );
		}

		$url = untrailingslashit( esc_url( $permalink ) );

		return compact( 'title_format', 'desc_format', 'url', 'permalink', 'permalink_format' );
	}

	/**
	 * Filter term link.
	 *
	 * @param string $termlink Term Link.
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return string
	 */
	private function get_termlink( $termlink, $taxonomy ) {
		if ( 'category' === $taxonomy && Helper::get_settings( 'general.cpseo_strip_category_base' ) ) {
			$termlink = str_replace( '/category/', '', $termlink );
		}

		if ( ( Conditional::is_woocommerce_active() || Conditional::is_classic_commerce_active() ) && 'product_cat' === $taxonomy && Helper::get_settings( 'general.cpseo_wc_remove_category_base' ) ) {
			$termlink = str_replace( 'product-category', '', $termlink );
		}

		return $termlink;
	}

	/**
	 * Whether to add ancestors in taxonomy page.
	 *
	 * @param int    $term_id  Term ID.
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return array
	 */
	private function get_ancestors( $term_id, $taxonomy ) {
		$slugs = [];

		if ( ( Conditional::is_woocommerce_active() || Conditional::is_classic_commerce_active() ) && 'product_cat' === $taxonomy && Helper::get_settings( 'general.cpseo_wc_remove_category_parent_slugs' ) ) {
			return $slugs;
		}

		$ancestors = get_ancestors( $term_id, $taxonomy, 'taxonomy' );
		foreach ( (array) $ancestors as $ancestor ) {
			$ancestor_term = get_term( $ancestor, $taxonomy );
			$slugs[]       = $ancestor_term->slug;
		}

		return array_reverse( $slugs );
	}

	/**
	 * Get user data for SERP preview.
	 *
	 * @return array
	 */
	private function get_user_data() {
		global $user_id, $wp_rewrite;

		$title_format = Helper::get_settings( 'titles.cpseo_author_archive_title' );
		$desc_format  = Helper::get_settings( 'titles.cpseo_author_archive_description' );
		$title_format = $title_format ? $title_format : '%author%';

		Rewrite::change_author_base();
		$permalink        = untrailingslashit( esc_url( get_author_posts_url( $user_id ) ) );
		$link             = $wp_rewrite->get_author_permastruct();
		$permalink_format = empty( $link ) ? $permalink : $this->get_author_permalink( $link );
		$url              = untrailingslashit( esc_url( $permalink ) );

		return compact( 'title_format', 'desc_format', 'url', 'permalink', 'permalink_format' );
	}

	/**
	 * Get user permalink
	 *
	 * @param  string $link Permalink structure.
	 * @return string
	 */
	private function get_author_permalink( $link ) {
		$link = str_replace( '%author%', '%postname%', $link );
		return home_url( user_trailingslashit( $link ) );
	}

	/**
	 * Get Snippet HTML for SERP preview.
	 */
	private function get_snippet_html() {
		$snippet_data = $this->get_snippet_data();
		if ( ! $snippet_data || ! isset( $snippet_data['data'] ) ) {
			return false;
		}

		$data         = $snippet_data['data'];
		$rating       = isset( $data['rating'] ) ? $data['rating'] : '';
		$rating_count = isset( $data['rating_count'] ) ? $data['rating_count'] : '';
		unset( $data['rating'] );
		unset( $data['rating_count'] );

		if ( isset( $data['price'] ) && isset( $data['currency'] ) ) {
			$data['price'] = $data['currency'] . $data['price'];
			unset( $data['currency'] );
		}

		$html = [
			'type'    => $snippet_data['type'],
			'desktop' => $this->get_desktop_preview( $data, $rating, $rating_count ),
			'mobile'  => $this->get_mobile_preview( $data, $rating, $rating_count ),
		];

		return $html;
	}

	/**
	 * Get desktop preview.
	 *
	 * @param  array $data         Snippet data array.
	 * @param  int   $rating       Ratings.
	 * @param  int   $rating_count Rating count.
	 * @return string
	 */
	private function get_desktop_preview( $data, $rating, $rating_count ) {
		$preview = '';
		$labels  = [
			'price_range' => esc_html__( 'Price range: ', 'cpseo' ),
			'calories'    => esc_html__( 'Calories: ', 'cpseo' ),
			'in_stock'    => esc_html__( 'In stock', 'cpseo' ),
		];

		if ( $rating ) {
			$preview .= $this->get_ratings( $rating );
			/* translators: total reviews */
			$preview .= '<span class="serp-rating-label">' . sprintf( esc_html__( 'Rating: %s', 'cpseo' ), esc_html( $rating ) ) . '</span>';
			if ( $rating_count ) {
				/* translators: total reviews */
				$preview .= '<span class="serp-review-count"> - ' . sprintf( esc_html__( '%s reviews', 'cpseo' ), esc_html( $rating_count ) ) . '</span>';
			}
		}

		foreach ( $data as $key => $value ) {
			if ( ! $value ) {
				continue;
			}

			if ( ! in_array( $key, [ 'event_date', 'event_place', 'event_name' ], true ) ) {
				$preview .= '<span class="separator"> - </span>';
			}

			$preview .= '<span class="serp-' . $key . '">';
			if ( isset( $labels[ $key ] ) ) {
				$preview .= $labels[ $key ];
			}

			if ( 'in_stock' !== $key ) {
				$preview .= $value;
			}

			$preview .= '</span>';
		}

		return '<div class="serp-snippet-data">' . $preview . '</div>';
	}

	/**
	 * Get mobile preview.
	 *
	 * @param  array $data         Snippet data array.
	 * @param  int   $rating       Ratings.
	 * @param  int   $rating_count Rating count.
	 * @return string
	 */
	private function get_mobile_preview( $data, $rating, $rating_count ) {
		$labels = [
			'price'       => esc_html__( 'Price', 'cpseo' ),
			'price_range' => esc_html__( 'Price range', 'cpseo' ),
			'time'        => esc_html__( 'Cooking time', 'cpseo' ),
			'calories'    => esc_html__( 'Calories', 'cpseo' ),
			'in_stock'    => esc_html__( 'In Stock', 'cpseo' ),
			'event_date'  => esc_html__( 'Date', 'cpseo' ),
			'event_place' => esc_html__( 'Location', 'cpseo' ),
		];

		$preview = '';
		if ( $rating ) {
			$preview .= '<span class="inner-wrapper">';
			$preview .= '<span class="serp-mobile-label">';
			$preview .= esc_html__( 'Rating', 'cpseo' );
			$preview .= '</span>';
			$preview .= '<span class="serp-rating-count">' . esc_html( $rating ) . '</span>';

			$preview .= $this->get_ratings( $rating );

			if ( $rating_count ) {
				$preview .= '<span class="serp-review-count">(' . esc_html( $rating_count ) . ')</span>';
			}

			$preview .= '</span>';
		}

		foreach ( $data as $key => $value ) {
			if ( ! $value || 'event_name' === $key ) {
				continue;
			}

			$preview .= '<span class="inner-wrapper">';

			if ( isset( $labels[ $key ] ) ) {
				$preview .= '<span class="serp-mobile-label">';
				$preview .= $labels[ $key ];
				$preview .= '</span>';
			}

			if ( 'in_stock' !== $key ) {
				$preview .= '<span class="serp-' . $key . '">' . esc_html( $value ) . '</span>';
			}

			$preview .= '</span>';
		}

		return '<div class="serp-snippet-mobile">' . $preview . '</div>';
	}

	/**
	 * Get Star Ratings.
	 *
	 * @param int $rating Rating count.
	 */
	private function get_ratings( $rating ) {
		$html   = '';
		$rating = $rating * 20;

		for ( $i = 1; $i <= 5; $i++ ) {
			$html .= '<span class="dashicons dashicons-star-filled"></span>';
		}

		$html .= '<div class="serp-result" style="width:' . $rating . '%;">';
		for ( $i = 1; $i <= 5; $i++ ) {
			$html .= '<span class="dashicons dashicons-star-filled"></span>';
		}

		return '<span class="serp-rating serp-desktop-rating"><div class="serp-star-rating">' . $html . '</div></div></span>';
	}

	/**
	 * Get Snippet Data for SERP preview.
	 *
	 * @return array
	 */
	private function get_snippet_data() {
		global $post;
		setup_postdata( $post );

		// Get rich snippet.
		$snippet         = get_post_meta( $post->ID, 'cpseo_rich_snippet', true );
		$wp_review_total = get_post_meta( $post->ID, 'wp_review_total', true );

		if ( ! in_array( $snippet, [ 'recipe', 'product', 'event', 'restaurant', 'review', 'service', 'software' ], true ) && ! $wp_review_total ) {
			return false;
		}

		$snippet_data = [ 'type' => $snippet ];

		if ( 'product' === $post->post_type && ( Conditional::is_woocommerce_active() || Conditional::is_classic_commerce_active() ) ) {
			$product              = wc_get_product( $post->ID );
			$snippet_data['data'] = [
				'price'    => $product->get_price(),
				'currency' => get_woocommerce_currency_symbol(),
				'in_stock' => $product->get_stock_status(),
			];
		} else {
			if ( 'recipe' === $snippet ) {
				$hash = [
					'rating'   => 'recipe_rating',
					'time'     => 'recipe_totaltime',
					'calories' => 'recipe_calories',
				];
			} elseif ( 'product' === $snippet ) {
				$hash = [
					'price'    => 'product_price',
					'currency' => 'product_currency',
					'in_stock' => 'product_instock',
				];
			} elseif ( 'event' === $snippet ) {
				$hash = [
					'event_date'  => 'event_startdate',
					'event_name'  => 'name',
					'event_place' => 'event_address',
				];
			} elseif ( 'restaurant' === $snippet ) {
				$hash = [
					'price_range' => 'local_price_range',
				];
			} else {
				$hash = [
					'rating'       => $snippet . '_rating_value',
					'rating_count' => $snippet . '_rating_count',
				];
			}

			foreach ( $hash as $key => $value ) {
				$value = get_post_meta( $post->ID, 'cpseo_snippet_' . $value, true );
				if ( ! $value ) {
					continue;
				}

				if ( 'event_place' === $key ) {
					$value = implode( ', ', array_filter( $value ) );
				}

				if ( 'event_date' === $key ) {
					$value = date_i18n( 'j M Y', $value );
				}
				$snippet_data['data'][ $key ] = $value;
			}
		}

		// Get rating.
		if ( ! isset( $snippet_data['rating'] ) && function_exists( 'wp_review_show_total' ) ) {
			$wp_review_type_star = 'star' === get_post_meta( $post->ID, 'wp_review_type', true );
			if ( $wp_review_total && $wp_review_type_star ) {
				$snippet_data['data']['rating'] = $wp_review_total;
			}
		}

		if ( ! isset( $snippet_data['rating'] ) && ( Conditional::is_woocommerce_active() || Conditional::is_classic_commerce_active() ) && 'product' === $post->post_type ) {
			$product = wc_get_product( $post->ID );
			if ( $product->get_rating_count() > 0 ) {
				$snippet_data['data']['rating']       = $product->get_average_rating();
				$snippet_data['data']['rating_count'] = (string) $product->get_rating_count();
			}
		}

		return $snippet_data;
	}
}
