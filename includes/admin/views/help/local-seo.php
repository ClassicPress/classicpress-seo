<?php
/**
 * Local SEO tab.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin\Help
 */

use Classic_SEO\Helper;
?>
<h3><?php esc_html_e( 'Local SEO', 'cpseo' ); ?></h3>
<p>
	<?php esc_html_e( 'Local SEO is a way for you to rank better for searches made by people local to the area in which you operate. It is the best way for you to get your products and services in front of the local customers.', 'cpseo' ); ?>
</p>
<p>
	<?php esc_html_e( 'There are various methods for optimizing your website for local SEO but Classic SEO has many tools to help.', 'cpseo' ); ?>
</p>

<p>
	<?php esc_html_e( 'Make sure the Local SEO & Google Knowledge Graph module is enabled.', 'cpseo' ); ?>
</p>
<p>
	<?php
	printf(
		/* translators: link to local seo settings */
		__( 'Then, head over to <a href="%1$s">Classic SEO > Titles and Meta > Local SEO</a> and add more information about your Local Business like your Company Name, Logo, Email ID, Phone number, Address and Contact/About pages.', 'cpseo' ),
		Helper::get_admin_url( 'options-titles#setting-panel-local' )
	);
	?>
</p>

