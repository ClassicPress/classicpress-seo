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
				<th scope="col">Variable</th>
				<th scope="col" colspan="2">Description</th>
			</tr>
		</thead>
		<tbody>
			<tr><td>%sep%</td><td><strong>Separator Character</strong></td><td><span>Separator character, as set in the Title Settings</span></td></tr>
			<tr><td>%search_query%</td><td><strong>Search Query</strong></td><td><span>Search query (only available on search results page)</span></td></tr>
			<tr><td>%count(varname)%</td><td><strong>Counter</strong></td><td><span>Starts at 1 and increments by 1.</span></td></tr>
			<tr><td>%filename%</td><td><strong>File Name</strong></td><td><span>File Name of the attachment</span></td></tr>
			<tr><td>%sitename%</td><td><strong>Site Title</strong></td><td><span>Title of the site</span></td></tr>
			<tr><td>%sitedesc%</td><td><strong>Site Description</strong></td><td><span>Description of the site</span></td></tr>
			<tr><td>%currentdate%</td><td><strong>Current Date</strong></td><td><span>Current server date</span></td></tr>
			<tr><td>%currentday%</td><td><strong>Current Day</strong></td><td><span>Current server day</span></td></tr>
			<tr><td>%currentmonth%</td><td><strong>Current Month</strong></td><td><span>Current server month</span></td></tr>
			<tr><td>%currentyear%</td><td><strong>Current Year</strong></td><td><span>Current server year</span></td></tr>
			<tr><td>%currenttime%</td><td><strong>Current Time</strong></td><td><span>Current server time</span></td></tr>
			<tr><td>%currenttime(F jS, Y)%</td><td><strong>Current Time (advanced)</strong></td><td><span>Current server time with custom formatting pattern.</span></td></tr>
			<tr><td>%title%</td><td><strong>Post Title</strong></td><td><span>Title of the current post/page</span></td></tr>
			<tr><td>%parent_title%</td><td><strong>Post Title of parent page</strong></td><td><span>Title of the parent page of the current post/page</span></td></tr>
			<tr><td>%excerpt%</td><td><strong>Post Excerpt</strong></td><td><span>Excerpt of the current post (or auto-generated if it does not exist)</span></td></tr>
			<tr><td>%excerpt_only%</td><td><strong>Post Excerpt</strong></td><td><span>Excerpt of the current post (without auto-generation)</span></td></tr>
			<tr><td>%date%</td><td><strong>Date Published</strong></td><td><span>Publication date of the current post/page <strong>OR</strong> specified date on date archives</span></td></tr>
			<tr><td>%modified%</td><td><strong>Date Modified</strong></td><td><span>Last modification date of the current post/page</span></td></tr>
			<tr><td>%date(F jS, Y)%</td><td><strong>Date Published (advanced)</strong></td><td><span>Publish date with custom formatting pattern.</span></td></tr>
			<tr><td>%modified(F jS, Y)%</td><td><strong>Date Modified (advanced)</strong></td><td><span>Modified date with custom formatting pattern.</span></td></tr>
			<tr><td>%category%</td><td><strong>Post Category</strong></td><td><span>First category (alphabetically) associated to the current post <strong>OR</strong> current category on category archives</span></td></tr>
			<tr><td>%categories%</td><td><strong>Post Categories</strong></td><td><span>Comma-separated list of categories associated to the current post</span></td></tr>
			<tr><td>%categories(limit=3&amp;separator= | &amp;exclude=12,23)%</td><td><strong>Categories (advanced)</strong></td><td><span>Output list of categories associated to the current post, with customisation options.</span></td></tr>
			<tr><td>%tag%</td><td><strong>Post Tag</strong></td><td><span>First tag (alphabetically) associated to the current post <strong>OR</strong> current tag on tag archives</span></td></tr>
			<tr><td>%tags%</td><td><strong>Post Tags</strong></td><td><span>Comma-separated list of tags associated to the current post</span></td></tr>
			<tr><td>%tags(limit=3&amp;separator= | &amp;exclude=12,23)%</td><td><strong>Tags (advanced)</strong></td><td><span>Output list of tags associated to the current post, with customisation options.</span></td></tr>
			<tr><td>%term%</td><td><strong>Current Term</strong></td><td><span>Current term name</span></td></tr>
			<tr><td>%term_description%</td><td><strong>Term Description</strong></td><td><span>Current term description</span></td></tr>
			<tr><td>%customterm(taxonomy-name)%</td><td><strong>Custom Term (advanced)</strong></td><td><span>Custom term value.</span></td></tr>
			<tr><td>%customterm_desc(taxonomy-name)%</td><td><strong>Custom Term description</strong></td><td><span>Custom Term description.</span></td></tr>
			<tr><td>%userid%</td><td><strong>Author ID</strong></td><td><span>Author's user id of the current post, page or author archive.</span></td></tr>
			<tr><td>%name%</td><td><strong>Post Author</strong></td><td><span>Display author's nicename of the current post, page or author archive.</span></td></tr>
			<tr><td>%user_description%</td><td><strong>Author Description</strong></td><td><span>Author's biographical info of the current post, page or author archive.</span></td></tr>
			<tr><td>%id%</td><td><strong>Post ID</strong></td><td><span>ID of the current post/page</span></td></tr>
			<tr><td>%focuskw%</td><td><strong>Focus Keyword</strong></td><td><span>Focus Keyword of the current post</span></td></tr>
			<tr><td>%customfield(field-name)%</td><td><strong>Custom Field (advanced)</strong></td><td><span>Custom field value.</span></td></tr>
			<tr><td>%page%</td><td><strong>Page</strong></td><td><span>Page number with context (i.e. page 2 of 4). Only displayed on page 2 and above.</span></td></tr>
			<tr><td>%pagenumber%</td><td><strong>Page Number</strong></td><td><span>Current page number</span></td></tr>
			<tr><td>%pagetotal%</td><td><strong>Max Pages</strong></td><td><span>Max pages number</span></td></tr>
			<tr><td>%pt_single%</td><td><strong>Post Type Name Singular</strong></td><td><span>Name of current post type (singular)</span></td></tr>
			<tr><td>%pt_plural%</td><td><strong>Post Type Name Plural</strong></td><td><span>Name of current post type (plural)</span></td></tr>
		</tbody>
	</table>
</div>