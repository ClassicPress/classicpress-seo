<?php
/**
 * The Breadcrumbs Class.
 *
 * @since      0.1.8
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\RichSnippet

 */

namespace ClassicPress_SEO\RichSnippet;

use ClassicPress_SEO\Frontend\Breadcrumbs as BreadcrumbTrail;

defined( 'ABSPATH' ) || exit;

/**
 * Breadcrumbs class.
 */
class Breadcrumbs implements Snippet {

	/**
	 * Generate breadcrumbs JSON-LD.
	 *
	 * @link https://schema.org/BreadcrumbList
	 *
	 * @param array  $data   Array of JSON-LD data.
	 * @param JsonLD $jsonld JsonLD Instance.
	 *
	 * @return array
	 */
	public function process( $data, $jsonld ) {
		$crumbs = BreadcrumbTrail::get() ? BreadcrumbTrail::get()->get_crumbs() : false;
		if ( empty( $crumbs ) ) {
			return $data;
		}

		$entity = [
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => [],
		];

		foreach ( $crumbs as $index => $crumb ) {
			if ( ! empty( $crumb['hide_in_schema'] ) ) {
				continue;
			}

			$entity['itemListElement'][] = [
				'@type'    => 'ListItem',
				'position' => $index + 1,
				'item'     => [
					'@id'  => $crumb[1],
					'name' => $crumb[0],
				],
			];
		}

		$data['BreadcrumbList'] = apply_filters( 'cpseo/snippet/breadcrumb', $entity );

		return $data;
	}
}
