<?php
/**
 * The WooCommerce Product Class.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet

 */

namespace Classic_SEO\RichSnippet;

use Classic_SEO\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Product_WooCommerce class.
 */
class Product_WooCommerce {

	/**
	 * Attribute assigner.
	 *
	 * @var WC_Attributes
	 */
	private $attributes;

	/**
	 * Set product data for rich snippet.
	 *
	 * @param array  $entity Array of JSON-LD entity.
	 * @param JsonLD $jsonld JsonLD Instance.
	 */
	public function set_product( &$entity, $jsonld ) {
		$product          = wc_get_product( get_the_ID() );
		$this->attributes = new WC_Attributes( $product );

		if ( Helper::is_module_active( 'woocommerce' ) ) {
			$brands = \Classic_SEO\WooCommerce\Woocommerce::get_brands( $product->get_id() );

			// Brand.
			if ( ! empty( $brands ) ) {
				$brands          = $brands[0]->name;
				$entity['brand'] = [
					'@type' => 'Thing',
					'name'  => $brands,
				];
			}
		}

		$entity['url']         = $product->get_permalink();
		$entity['name']        = $product->get_name();
		$entity['description'] = $jsonld->get_product_desc( $product );
		$entity['sku']         = $product->get_sku() ? $product->get_sku() : '';
		$entity['category']    = Product::get_category( $product->get_id(), 'product_cat' );

		$this->set_related_products( $product->get_id(), $entity );
		$this->set_upsell_products( $product, $entity );
		$this->set_weight( $product, $entity );
		$this->set_dimensions( $product, $entity );
		$this->set_images( $product, $entity );
		$this->set_ratings( $product, $entity );
		$this->set_offers( $product, $entity, Product::get_seller( $jsonld ) );

		// GTIN numbers need product attributes.
		$this->attributes->assign_property( $entity, 'gtin8' );
		$this->attributes->assign_property( $entity, 'gtin12' );
		$this->attributes->assign_property( $entity, 'gtin13' );
		$this->attributes->assign_property( $entity, 'gtin14' );

		// Color.
		$this->attributes->assign_property( $entity, 'color' );

		// Remaining Attributes.
		$this->attributes->assign_remaining( $entity );
	}

	/**
	 * Set related products.
	 *
	 * @param int   $product_id Product ID.
	 * @param array $entity     Array of json-ld entity.
	 */
	private function set_related_products( $product_id, &$entity ) {
		$related_products = wc_get_related_products( $product_id, 5 );
		if ( empty( $related_products ) ) {
			return;
		}

		foreach ( $related_products as $related_id ) {
			$entity['isRelatedTo'][] = [
				'@type' => 'Product',
				'url'   => get_permalink( $related_id ),
				'name'  => get_the_title( $related_id ),
			];
		}
	}

	/**
	 * Set upsell products.
	 *
	 * @param object $product Product instance.
	 * @param array  $entity  Array of json-ld entity.
	 */
	private function set_upsell_products( $product, &$entity ) {
		$upsells = $product->get_upsell_ids();
		if ( empty( $upsells ) ) {
			return;
		}

		foreach ( $upsells as $upsell_id ) {
			$entity['isSimilarTo'][] = [
				'@type' => 'Product',
				'url'   => get_permalink( $upsell_id ),
				'name'  => get_the_title( $upsell_id ),
			];
		}
	}

	/**
	 * Set product weight.
	 *
	 * @param object $product Product instance.
	 * @param array  $entity  Array of json-ld entity.
	 */
	private function set_weight( $product, &$entity ) {
		if ( ! $product->has_weight() ) {
			return;
		}

		$hash = [
			'lbs' => 'LBR',
			'kg'  => 'KGM',
			'g'   => 'GRM',
			'oz'  => 'ONZ',
		];
		$unit = get_option( 'woocommerce_weight_unit' );

		$entity['weight'] = [
			'@type'    => 'QuantitativeValue',
			'unitCode' => isset( $hash[ $unit ] ) ? $hash[ $unit ] : 'LBR',
			'value'    => $product->get_weight(),
		];
	}

	/**
	 * Set product dimension.
	 *
	 * @param object $product Product instance.
	 * @param array  $entity  Array of json-ld entity.
	 */
	private function set_dimensions( $product, &$entity ) {
		if ( ! $product->has_dimensions() ) {
			return;
		}

		$hash = [
			'in' => 'INH',
			'm'  => 'MTR',
			'cm' => 'CMT',
			'mm' => 'MMT',
			'yd' => 'YRD',
		];
		$unit = get_option( 'woocommerce_dimension_unit' );
		$code = isset( $hash[ $unit ] ) ? $hash[ $unit ] : '';

		$entity['height'] = [
			'@type'    => 'QuantitativeValue',
			'unitCode' => $code,
			'value'    => $product->get_height(),
		];

		$entity['width'] = [
			'@type'    => 'QuantitativeValue',
			'unitCode' => $code,
			'value'    => $product->get_width(),
		];

		$entity['depth'] = [
			'@type'    => 'QuantitativeValue',
			'unitCode' => $code,
			'value'    => $product->get_length(),
		];
	}

	/**
	 * Set product images.
	 *
	 * @param object $product Product instance.
	 * @param array  $entity  Array of json-ld entity.
	 */
	private function set_images( $product, &$entity ) {
		if ( ! $product->get_image_id() ) {
			return;
		}

		$image             = wp_get_attachment_image_src( $product->get_image_id(), 'single-post-thumbnail' );
		$entity['image'][] = [
			'@type'  => 'ImageObject',
			'url'    => $image[0],
			'height' => $image[2],
			'width'  => $image[1],
		];

		$gallery = $product->get_gallery_image_ids();
		foreach ( $gallery as $image_id ) {
			$image             = wp_get_attachment_image_src( $image_id, 'single-post-thumbnail' );
			$entity['image'][] = [
				'@type'  => 'ImageObject',
				'url'    => $image[0],
				'height' => $image[2],
				'width'  => $image[1],
			];
		}
	}

	/**
	 * Set product ratings.
	 *
	 * @param object $product Product instance.
	 * @param array  $entity  Array of json-ld entity.
	 */
	private function set_ratings( $product, &$entity ) {
		if ( $product->get_rating_count() < 1 ) {
			return;
		}

		// Aggregate Rating.
		$entity['aggregateRating'] = [
			'@type'       => 'AggregateRating',
			'ratingValue' => $product->get_average_rating(),
			'bestRating'  => '5',
			'ratingCount' => $product->get_rating_count(),
			'reviewCount' => $product->get_review_count(),
		];

		// Reviews.
		$comments  = get_comments([
			'post_type' => 'product',
			'post_id'   => get_the_ID(),
			'status'    => 'approve',
			'parent'    => 0,
		]);
		$permalink = $product->get_permalink();

		foreach ( $comments as $comment ) {
			$entity['review'][] = [
				'@type'         => 'Review',
				'@id'           => $permalink . '#li-comment-' . $comment->comment_ID,
				'description'   => $comment->comment_content,
				'datePublished' => $comment->comment_date,
				'reviewRating'  => [
					'@type'       => 'Rating',
					'ratingValue' => intval( get_comment_meta( $comment->comment_ID, 'rating', true ) ),
				],
				'author'        => [
					'@type' => 'Person',
					'name'  => $comment->comment_author,
					'url'   => $comment->comment_author_url,
				],
			];
		}
	}

	/**
	 * Set product offers.
	 *
	 * @param object $product Product instance.
	 * @param array  $entity  Array of json-ld entity.
	 * @param array  $seller  Seller info.
	 */
	private function set_offers( $product, &$entity, $seller ) {
		if ( '' === $product->get_price() ) {
			return;
		}

		if ( true === $this->set_offers_variable( $product, $entity, $seller ) ) {
			return;
		}

		$offer = [
			'@type'           => 'Offer',
			'price'           => $product->get_price() ? $product->get_price() : '0',
			'priceCurrency'   => get_woocommerce_currency(),
			'availability'    => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
			'itemCondition'   => 'NewCondition',
			'seller'          => $seller,
			'url'             => $product->get_permalink(),
			'priceValidUntil' => ! empty( $product->get_date_on_sale_to() ) ? date_i18n( 'Y-m-d', strtotime( $product->get_date_on_sale_to() ) ) : '',
		];

		$this->attributes->assign_property( $offer, 'itemCondition' );
		$entity['offers'] = $offer;
	}

	/**
	 * Set product variable offers.
	 *
	 * @param object $product Product instance.
	 * @param array  $entity  Array of json-ld entity.
	 * @param array  $seller  Seller info.
	 */
	private function set_offers_variable( $product, &$entity, $seller ) {
		$permalink  = $product->get_permalink();
		$variations = $this->has_variations( $product );
		if ( false === $variations ) {
			return false;
		}

		$entity['offers'] = [];
		foreach ( $variations as $variation ) {
			$price_valid_until = get_post_meta( $variation['variation_id'], '_sale_price_dates_to', true );

			$offer = [
				'@type'           => 'Offer',
				'description'     => strip_tags( $variation['variation_description'] ),
				'price'           => $variation['display_price'],
				'priceCurrency'   => get_woocommerce_currency(),
				'availability'    => $variation['is_in_stock'] ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
				'itemCondition'   => 'NewCondition',
				'seller'          => $seller,
				'priceValidUntil' => $price_valid_until ? date_i18n( 'Y-m-d', $price_valid_until ) : '',
				'url'             => $permalink,
			];

			// Generate a unique variation ID.
			$this->set_variation_unique_id( $variation, $offer, $permalink );

			// Look for itemCondition override by variation.
			$this->set_variation_condition( $variation, $offer );
			$entity['offers'][] = $offer;
		}

		return true;
	}

	/**
	 * Set product variation condition.
	 *
	 * @param object $variation Product variation.
	 * @param array  $entity    Array of json-ld entity.
	 * @param string $permalink Permalink of product.
	 */
	private function set_variation_unique_id( $variation, &$entity, $permalink ) {
		if ( '' !== $variation['sku'] ) {
			$entity['sku'] = $variation['sku'];
			$entity['@id'] = $permalink . '#' . $variation['sku'];
			return;
		}

		foreach ( $variation['attributes'] as $key => $value ) {
			if ( '' !== $value ) {
				$entity['@id'] = $permalink . '#' . substr( $key, 10 ) . '-' . filter_var( $value, FILTER_SANITIZE_URL );
			}
		}
	}

	/**
	 * Set product variation condition.
	 *
	 * @param object $variation Product variation.
	 * @param array  $entity  Array of json-ld entity.
	 */
	private function set_variation_condition( $variation, &$entity ) {
		foreach ( $variation['attributes'] as $key => $value ) {
			if ( stristr( $key, 'itemCondition' ) ) {
				$entity['itemCondition'] = $value;
			}
		}
	}

	/**
	 * If product is variable, send variations.
	 *
	 * @param object $product Current product.
	 *
	 * @return array|boolean
	 */
	private function has_variations( $product ) {
		if ( ! $product->is_type( 'variable' ) ) {
			return false;
		}

		$variations = $product->get_available_variations();
		return ! empty( $variations ) ? $variations : false;
	}
}
