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
	<?php esc_html_e( 'Classic SEO is a Search Engine Optimization plugin built specifically for ClassicPress. It is based on Rank Math but there are some key differences both in the way it works and "behind the scenes".', 'cpseo' ); ?>
</p>
<p>
	<?php esc_html_e( 'While Rank Math provides support for bbPress, BuddyPress and AMP, Classic SEO does not and probably never will. Likewise, it is unlikely that Classic SEO will ever feature the ability to edit .htaccess and robots.txt.', 'cpseo' ); ?>
</p>
<p>
	<?php esc_html_e( 'A couple of other differences are that Classic SEO does not have the SEO Analysis tool and it does not support the Gutenberg block editor.', 'cpseo' ); ?>
</p>
<h3><?php esc_html_e( 'Found a bug?', 'cpseo' ); ?></h3>
<p>
	<?php esc_html_e( 'It should be noted that Classic SEO is still in the early stages of development and bugs are likely.', 'cpseo' ); ?>
</p>
<p>
	<?php 
	printf(
		/* translators: 1: Link to ClassicPress Forums 2: Link to GitHub */
		__('If you do find what looks like a bug, please either report it on the <a rel="nofollow noopener noreferrer" href="%1$s" target="_blank">ClassicPress forums</a> or <a rel="nofollow noopener noreferrer" href="%2$s" target="_blank">open an issue on GitHub</a>.', 'cpseo' ),
		'https://forums.classicpress.net/tags/classic-seo', 'https://github.com/ClassicPress-plugins/classicpress-seo/issues'); ?>
</p>
<h3><?php esc_html_e( 'Plans for future releases', 'cpseo' ); ?></h3>
<p>
	<?php 
	printf(
		/* translators: 1: Link to Classic Commerce */
		__('A key future addition to Classic SEO will be support for <a rel="nofollow noopener noreferrer" href="%1$s" target="_blank">Classic Commerce</a>.', 'cpseo' ),
		'https://github.com/ClassicPress-plugins/classic-commerce'); ?>
</p>
<p>
	<?php esc_html_e( 'It is also planned to improve support for the most popular page builders such as Beaver Builder, Divi and Elementor.', 'cpseo' ); ?>
</p>
<p>
	<?php 
	printf(
		/* translators: 1: Link to ClassicPress Forums */
		__('If there is any feature you\'d like to see added to Classic SEO, post a message on the <a rel="nofollow noopener noreferrer" href="%1$s" target="_blank">ClassicPress forums</a>.', 'cpseo' ),
		'https://forums.classicpress.net/tags/classic-seo'); ?>
</p>
<p>
	<?php esc_html_e( 'But remember, no one around here likes bloat or resource-hungry features. We aim to keep Classic SEO as lightweight as possible.', 'cpseo' ); ?>
</p>


