<?php
/**
 * The webmaster variable template.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Settings
 */

?>
<div class="cmb-row cpseo-rss-variables">

	<h3><?php esc_html_e( 'Available variables', 'cpseo' ); ?> </h3>

	<table class="wp-list-table widefat striped">
	<thead>
		<tr>
			<th scope="col"> <?php esc_html_e( 'Variable', 'cpseo' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Description', 'cpseo' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>%AUTHORLINK%</td>
			<td><?php esc_html_e( 'A link to the archive for the post author, with the authors name as anchor text.', 'cpseo' ); ?></td>
		</tr>
		<tr>
			<td>%POSTLINK%</td>
			<td><?php esc_html_e( 'A link to the post, with the title as anchor text.', 'cpseo' ); ?></td>
		</tr>
		<tr>
			<td>%BLOGLINK%</td>
			<td><?php esc_html_e( "A link to your site, with your site's name as anchor text.", 'cpseo' ); ?></td>
		</tr>
		<tr>
			<td>%BLOGDESCLINK%</td>
			<td><?php esc_html_e( "A link to your site, with your site's name and description as anchor text.", 'cpseo' ); ?></td>
		</tr>
		<tr>
			<td>%FEATUREDIMAGE%</td>
			<td><?php esc_html_e( 'Featured image of the article.', 'cpseo' ); ?></td>
		</tr>
	</tbody>
	</table>

</div>
