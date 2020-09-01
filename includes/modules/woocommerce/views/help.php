<?php
/**
 * WooCommerce general settings.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\WooCommerce
 */

use Classic_SEO\Helper;
?>

<h3><?php esc_html_e( 'Classic Commerce', 'cpseo' ); ?></h3>

<p><?php esc_html_e( 'SEO is an important part of any website and that\'s especially true for a Classic Commerce store.', 'cpseo' ); ?></p>

<p><?php esc_html_e( 'With the Classic SEO plugin, you can easily optimize your Classic Commerce store in general, and product pages in particular.', 'cpseo' ); ?></p>

<p><strong><?php esc_html_e( 'Optimizing Your Classic Commerce Store', 'cpseo' ); ?></strong></p>

<p>
	<?php
	printf(
		/* translators: link to local seo settings */
		__( 'Classic SEO can help you make your product category or tag archives `noindex`. You can do that from <a href="%1$s">ClassicPress Dashboard > Classic SEO > Titles & Meta > Product Categories</a>', 'cpseo' ),
		Helper::get_admin_url( 'options-titles#setting-panel-taxonomy-product_cat' )
	);
	?>
</p>

<p><?php esc_html_e( 'or', 'cpseo' ); ?></p>

<p>
	<a href="<?php echo Helper::get_admin_url( 'options-titles#setting-panel-taxonomy-product_tag' ); ?>"><?php esc_html_e( 'ClassicPress Dashboard > Classic SEO > Titles & Meta > Product Tags.', 'cpseo' ); ?></a>
</p>

<p><strong><?php esc_html_e( 'Optimizing Your Product Pages', 'cpseo' ); ?></strong></p>

<p>
	<?php
	printf(
		/* translators: link to local seo settings */
		__( 'You can customize and automate the SEO Title/Description generation easily as well. Go to <a href="%1$s">ClassicPress Dashboard > Classic SEO > Titles & Meta > Products</a>', 'cpseo' ),
		Helper::get_admin_url( 'options-titles#setting-panel-post-type-product' )
	);
	?>
</p>

<p><?php esc_html_e( 'Classic SEO also lets you easily add rich snippets to your product.', 'cpseo' ); ?></p>

<p><?php esc_html_e( 'Rich snippets can be added from within the product pages. Simply click on the Rich Snippet tab and choose the Product snippet type option.', 'cpseo' ); ?></p>

<p><strong><?php esc_html_e( 'Optimizing Your Product URLs', 'cpseo' ); ?></strong></p>

<p><?php esc_html_e( 'Classic SEO offers you the option to remove the category base from your product archive URLs so the URLs are cleaner, more SEO friendly and easier to remember.', 'cpseo' ); ?></p>

<p>
	<?php
	printf(
		/* translators: link to local seo settings */
		__( 'To access those options, head over to <a href="%1$s">ClassicPress Dashboard > Classic SEO > General Settings > Classic Commerce</a>.', 'cpseo' ),
		Helper::get_admin_url( 'options-general#setting-panel-woocommerce' )
	);
	?>
</p>
