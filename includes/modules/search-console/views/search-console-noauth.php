<?php
/**
 * Search console no auth page.
 *
 * @package    Classic_SEO
 */

use Classic_SEO\Helper;
?>
<br>
<div class="cpseo-notice notice notice-error inline">
	<p>
		<?php /* translators: admin screen link */ ?>
		<?php printf( wp_kses_post( __( 'Please navigate to <a href="%s">SEO > Settings > Search Console</a> to authorize access to Google Search Console.', 'cpseo' ) ), esc_url( Helper::get_admin_url( 'options-general#setting-panel-search-console' ) ) ); ?>
	</p>
</div>
