<?php
/**
 * WooCommerce general settings.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\WooCommerce
 */

use Classic_SEO\Helper;
?>

<h3><?php esc_html_e( 'WooCommerce', 'cpseo' ); ?></h3>

<p><?php esc_html_e( 'SEO is the backbone of any website and it couldn\'t be more true for a WooCommerce store.', 'cpseo' ); ?></p>

<p><?php esc_html_e( 'When you sell something online, you want people to buy it. And, SEO is the best way to do so in the long run.', 'cpseo' ); ?></p>

<p><?php esc_html_e( 'With the Classic SEO plugin, you can easily optimize your WooCommerce store in general and product pages in particular.', 'cpseo' ); ?></p>

<p><strong><?php esc_html_e( 'Optimizing Your WooCommerce Store', 'cpseo' ); ?></strong></p>

<p>
	<?php
	printf(
		/* translators: link to local seo settings */
		__( 'Classic SEO can help you make your product category or tag archives `noindex`. You can do that from <a href="%1$s">WordPress Dashboard > Classic SEO > Titles & Meta > Product Categories</a>', 'cpseo' ),
		Helper::get_admin_url( 'options-titles#setting-panel-taxonomy-product_cat' )
	);
	?>
</p>

<p><?php esc_html_e( 'or', 'cpseo' ); ?></p>

<p>
	<a href="<?php echo Helper::get_admin_url( 'options-titles#setting-panel-taxonomy-product_tag' ); ?>"><?php esc_html_e( 'WordPress Dashboard > Classic SEO > Titles & Meta > Product Tags.', 'cpseo' ); ?></a>
</p>

<p><img src="<?php echo cpseo()->plugin_url() . 'assets/admin/img/help/product-archive-settings.jpg'; ?>" alt="make categories noindex" /></p>

<p><strong><?php esc_html_e( 'Optimizing Your Product Pages', 'cpseo' ); ?></strong></p>

<p>
	<?php
	printf(
		/* translators: link to local seo settings */
		__( 'You can customize and automate the SEO Title/Description generation easily as well. Just head over to <a href="%1$s">WordPress Dashboard > Classic SEO > Titles & Meta > Products</a>', 'cpseo' ),
		Helper::get_admin_url( 'options-titles#setting-panel-post-type-product' )
	);
	?>
</p>

<p><img src="<?php echo cpseo()->plugin_url() . 'assets/admin/img/help/individual-product-settings.jpg'; ?>" alt="product seo title" /></p>

<p><?php esc_html_e( 'You can also add rich snippets to your product pages easily with Classic SEO, apart from doing the regular SEO like you would do on posts.', 'cpseo' ); ?></p>

<p>
	<?php
	printf(
		/* translators: link to local seo settings */
		__( 'Do that from the product pages themeselve. Go to <a href="%1$s">WordPress Dashboard > Products > Add New</a>', 'cpseo' ),
		admin_url( 'post-new.php?post_type=product' )
	);
	?>
</p>

<p><?php esc_html_e( 'And, choose the product schema from the Rich Snippets tab.', 'cpseo' ); ?></p>

<p><img src="<?php echo cpseo()->plugin_url() . 'assets/admin/img/help/product-rich-snippets.jpg'; ?>" alt="product rich snippets" /></p>

<p><strong><?php esc_html_e( 'Optimizing Your Product URLs', 'cpseo' ); ?></strong></p>

<p><?php esc_html_e( 'Classic SEO offers you to remove category base from your product archive URLs so the URLs are cleaner, more SEO friendly and easier to remember.', 'cpseo' ); ?></p>

<p>
	<?php
	printf(
		/* translators: link to local seo settings */
		__( 'To access those options, head over to <a href="%1$s">WordPress Dashboard > Classic SEO > General Settings > WooCommerce</a>.', 'cpseo' ),
		Helper::get_admin_url( 'options-general#setting-panel-woocommerce' )
	);
	?>
</p>

<p><img src="<?php echo cpseo()->plugin_url() . 'assets/admin/img/help/woocommerce-url-settings.jpg'; ?>" alt="product category base" /></p>
