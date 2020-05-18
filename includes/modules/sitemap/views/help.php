<?php
/**
 * Help Sitemaps tab.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Sitemap
 */

?>
<h3><?php esc_html_e( 'Sitemaps', 'cpseo' ); ?></h3>

<p><?php esc_html_e( 'XML sitemaps are files that help search engines find and index all of your content.', 'cpseo' ); ?></p>

<p><?php esc_html_e( 'In many cases, if you have good internal linking, search engines will be able to discover content on your site without the need for a sitemap. Nevertheless, a sitemap is still recommended.', 'cpseo' ); ?></p>

<p><?php esc_html_e( 'If your internal linking isn\'t perfect, you can easily end up with pages that don\'t have any links pointing to them, making them hard to find. ', 'cpseo' ); ?></p>

<p><?php esc_html_e( 'An XML sitemap lists your website\'s important pages, making sure search engines can find and crawl them all.', 'cpseo' ); ?></p>

<h3><?php esc_html_e( 'Where is my sitemap?', 'cpseo' ); ?></h3>

<p>
	<?php
	printf(
		/* translators: link to sitemap */
		__( 'Classic SEO creates a file called sitemap_index.xml which can be viewed here <a href="%1$s" target="_blank">%2$s</a>.', 'cpseo' ),
		get_site_url() . '/sitemap_index.xml',
		get_site_url() . '/sitemap_index.xml'
	);
	?>
</p>