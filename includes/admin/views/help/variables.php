<?php
/**
 * Local SEO tab.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin\Help
 */

use Classic_SEO\Helper;
?>

<div class="help_variables">
	<table class="cpseo-help cpseo-table-scrollable">
		<thead>
			<tr>
				<th scope="col"><?php esc_html_e( 'Variable', 'cpseo' ); ?></th>
				<th scope="col" colspan="2"><?php esc_html_e( 'Description', 'cpseo' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php esc_html_e( '%sep%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Separator Character', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Separator character, as set in the Title Settings', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%search_query%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Search Query', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Search query (only available on search results page)', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%count(varname)%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Counter', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Starts at 1 and increments by 1.', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%filename%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'File Name', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'File Name of the attachment', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%sitename%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Site Title', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Title of the site', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%sitedesc%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Site Description', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Description of the site', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%currentdate%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Current Date', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Current server date', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%currentday%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Current Day', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Current server day', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%currentmonth%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Current Month', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Current server month', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%currentyear%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Current Year', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Current server year', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%currenttime%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Current Time', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Current server time', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%currenttime(F jS, Y)%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Current Time (advanced)', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Current server time with custom formatting pattern.', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%title%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Post Title', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Title of the current post/page', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%parent_title%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Post Title of parent page', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Title of the parent page of the current post/page', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%excerpt%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Post Excerpt', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Excerpt of the current post (or auto-generated if it does not exist)', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%excerpt_only%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Post Excerpt', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Excerpt of the current post (without auto-generation)', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%date%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Date Published', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Publication date of the current post/page <strong>OR</strong> specified date on date archives', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%modified%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Date Modified', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Last modification date of the current post/page', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%date(F jS, Y)%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Date Published (advanced)', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Publish date with custom formatting pattern.', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%modified(F jS, Y)%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Date Modified (advanced)', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Modified date with custom formatting pattern.', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%category%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Post Category', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'First category (alphabetically) associated to the current post <strong>OR</strong> current category on category archives', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%categories%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Post Categories', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Comma-separated list of categories associated to the current post', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%categories(limit=3&amp;separator= | &amp;exclude=12,23)%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Categories (advanced)', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Output list of categories associated to the current post, with customisation options.', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%tag%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Post Tag', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'First tag (alphabetically) associated to the current post <strong>OR</strong> current tag on tag archives', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%tags%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Post Tags', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Comma-separated list of tags associated to the current post', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%tags(limit=3&amp;separator= | &amp;exclude=12,23)%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Tags (advanced)', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Output list of tags associated to the current post, with customisation options.', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%term%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Current Term', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Current term name', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%term_description%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Term Description', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Current term description', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%customterm(taxonomy-name)%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Custom Term (advanced)', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Custom term value.', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%customterm_desc(taxonomy-name)%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Custom Term description', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Custom Term description.', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%userid%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Author ID', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Author\'s user id of the current post, page or author archive.', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%name%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Post Author', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Display author\'s nicename of the current post, page or author archive.', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%user_description%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Author Description', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Author\'s biographical info of the current post, page or author archive.', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%id%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Post ID', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'ID of the current post/page', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%focuskw%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Focus Keyword', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Focus Keyword of the current post', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%customfield(field-name)%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Custom Field (advanced)', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Custom field value.', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%page%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Page', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Page number with context (i.e. page 2 of 4). Only displayed on page 2 and above.', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%pagenumber%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Page Number', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Current page number', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%pagetotal%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Max Pages', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Max pages number', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%pt_single%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Post Type Name Singular', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Name of current post type (singular)', 'cpseo' ); ?></span></td>
			</tr>
			<tr>
				<td><?php esc_html_e( '%pt_plural%', 'cpseo' ); ?></td>
				<td><strong><?php esc_html_e( 'Post Type Name Plural', 'cpseo' ); ?></strong></td>
				<td><span><?php esc_html_e( 'Name of current post type (plural)', 'cpseo' ); ?></span></td>
			</tr>
		</tbody>
	</table>
</div>