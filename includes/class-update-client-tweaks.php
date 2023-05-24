<?php

namespace Classic_SEO;

use Classic_SEO\Helper;

class updateClientTweaks {		

	public function __construct() {
		// Fix images folder for Update Manager.
		add_filter( 'codepotent_update_manager_classicpress-seo/classicpress-seo.php_image_path', [ $this, 'fix_update_manager_images' ] );
		add_filter( 'codepotent_update_manager_classicpress-seo/classicpress-seo.php_image_url', [ $this, 'fix_update_manager_images' ] );
		add_filter( 'codepotent_update_manager_filter_classicpress-seo/classicpress-seo.php_client_request', [ $this, 'anonymous_data_collection' ] );
	}

	/**
	 * Fix images folder for Update Manager.
	 */
	public function fix_update_manager_images( $folder ) {
	trigger_error(preg_replace( '/' . basename( CPSEO_PATH ) . '\/images$/', basename( CPSEO_PATH ) . '/assets/images', $folder ));
		return preg_replace( '/' . basename( CPSEO_PATH ) . '\/images$/', basename( CPSEO_PATH ) . '/assets/images', $folder );
	}

	/**
	 * Handle anonymous data collectin.
	 */
	public function anonymous_data_collection( $body ) {
		if( ! Helper::get_settings( 'general.cpseo_usage_tracking' ) ) {
			$body['sfum'] = 'no-log';
		}
		return $body;
	}

}

new updateClientTweaks;