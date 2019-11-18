<?php
/**
 * Help getting started tab.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin\Help
 */


?>
<h3><?php esc_html_e( 'Getting started with Classic SEO', 'cpseo' ); ?></h3>
<p>
	<?php esc_html_e( 'Classic SEO is a Search Engine Optimization plugin built specifically for ClassicPress. It is based on Rank Math but there some differences both in the way it works and "behind the scenes".', 'cpseo' ); ?>
</p>
<p>
	<?php esc_html_e( 'While Rank Math provides support for bbPress, BuddyPress and AMP, Classic SEO does not and probably never will. Likewise, it is unlikely that Classic SEO will ever feature the ability to edit .htaccess and robots.txt.', 'cpseo' ); ?>
</p>
<p>
	<?php esc_html_e( 'Another difference is that Classic SEO does not have the SEO Analysis tool.', 'cpseo' ); ?>
</p>
<h3><?php esc_html_e( 'Found a bug?', 'cpseo' ); ?></h3>
<p>
	<?php esc_html_e( 'It should be noted that Classic SEO is still in the  early stages of development and bugs are likely.', 'cpseo' ); ?>
</p>
<p>
	<?php 
	printf(
		/* translators:  */
		__('If you do find what looks like a bug, please either report it on the <a rel="nofollow noopener noreferrer" href="%1$s" target="_blank">ClassicPress forums</a> or <a rel="nofollow noopener noreferrer" href="%2$s" target="_blank">open an issue on GitHub</a>.', 'cpseo' ),
		'https://forums.classicpress.net/', 'https://github.com/ClassicPress-research/classicpress-seo/issues'); ?>
</p>
<h3><?php esc_html_e( 'Plans for future releases', 'cpseo' ); ?></h3>
<p>
	<?php esc_html_e( 'In the next release of Classic SEO, it is planned to add the Google Search Console feature where data such as average ranking position, click through rate, and keywords will be available from within Classic SEO.', 'cpseo' ); ?>
</p>
<p>
	<?php 
	printf(
		/* translators:  */
		__('Classic SEO will also provide support for <a rel="nofollow noopener noreferrer" href="%1$s" target="_blank">Classic Commerce</a>.', 'cpseo' ),
		'https://github.com/ClassicPress-research/classic-commerce'); ?>
</p>



