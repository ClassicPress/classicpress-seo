<?php
/**
 * Help page template.
 *
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\Admin
 */



$is_network_admin  = is_network_admin();
$is_network_active = ClassicPress_SEO\Helper::is_plugin_active_for_network();
$current_tab       = 'tab_cpseo_help_variables';

$plugin_settings_tabs = array(
	'tab_cpseo_help_variables' => __( "Variables", "cpseo" )
);
echo $this->cpseo_admin_header();
?>

<div class="cpseo-option">
	<h2><?php _e( 'Help', 'cpseo' ); ?></h2>
	
	<div id="cpseo-tabs" class="wrap">

		<?php
			echo '<div class="nav-tab-wrapper">';
			foreach ( $plugin_settings_tabs as $tab_key => $tab_caption ) {
				echo '<a id="'. $tab_key .'-tab" class="nav-tab" href="?page=cpseo-help#tab=' . $tab_key . '">' . $tab_caption . '</a>'; 
			}
			echo '</div>';

		?>
		<div class="cpseo-tab <?php if ($current_tab == 'tab_cpseo_help_variables') { echo 'active'; } ?>" id="tab_cpseo_help_variables">
			<table class="cpseo-help cpseo-table-scrollable">
				<thead>
					<tr>
						<th scope="col">Variable</th>
						<th scope="col">Description</th>
					</tr>
				</thead>
				<tbody>
					<tr><td>%%sep%%</td><td>Separator (eg: - )</td></tr>
					<tr><td>%%sitetitle%%</td><td>Site Title</td></tr>
					<tr><td>%%tagline%%</td><td>Tagline</td></tr>
					<tr><td>%%post_title%% (alias %%title%%)</td><td>Post Title (post, page, custom post type)</td></tr>
					<tr><td>%%post_excerpt%%</td><td>Post excerpt</td></tr>
					<tr><td>%%post_date%%</td><td>Post date</td></tr>
					<tr><td>%%post_modified_date%%</td><td>Last modified post date</td></tr>
					<tr><td>%%post_author%%</td><td>Post author</td></tr>
					<tr><td>%%post_category%%</td><td>Post category</td></tr>
					<tr><td>%%post_tag%%</td><td>Post tag</td></tr>
					<tr><td>%%_category_title%%</td><td>Category title</td></tr>
					<tr><td>%%_category_description%%</td><td>Category description</td></tr>
					<tr><td>%%tag_title%%</td><td>Tag title</td></tr>
					<tr><td>%%tag_description%%</td><td>Tag description</td></tr>
					<tr><td>%%term_title%%</td><td>Term title</td></tr>
					<tr><td>%%term_description%%</td><td>Term description</td></tr>
					<tr><td>%%search_keywords%%</td><td>Search keywords</td></tr>
					<tr><td>%%current_pagination%%</td><td>Current number page</td></tr>
					<tr><td>%%cpt_plural%%</td><td>Plural Post Type Archive name</td></tr>
					<tr><td>%%archive_title%%</td><td>Archive title</td></tr>
					<tr><td>%%archive_date%%</td><td>Date Archive</td></tr>
					<tr><td>%%archive_date_day%%</td><td>Day Archive date</td></tr>
					<tr><td>%%archive_date_month%%</td><td>Month Archive title</td></tr>
					<tr><td>%%archive_date_year%%</td><td>Year Archive title</td></tr>
					<tr><td>%%_cf_your_custom_field_name%%</td><td>Custom fields from post, page or post type</td></tr>
					<tr><td>%%_ct_your_custom_taxonomy_slug%%</td><td>Custom term taxonomy from post, page or post type</td></tr>
					<tr><td>%%wc_single_cat%%</td><td>Single product category</td></tr>
					<tr><td>%%wc_single_tag%%</td><td>Single product tag</td></tr>
					<tr><td>%%wc_single_short_desc%%</td><td>Single product short description</td></tr>
					<tr><td>%%wc_single_price%%</td><td>Single product price</td></tr>
					<tr><td>%%wc_single_price_exc_tax%%</td><td>Single product price taxes excluded</td></tr>
					<tr><td>%%wc_sku%%</td><td>Single SKU product</td></tr>
					<tr><td>%%currentday%%</td><td>Current day</td></tr>
					<tr><td>%%currentmonth%%</td><td>Current month</td></tr>
					<tr><td>%%currentyear%%</td><td>Current year</td></tr>
					<tr><td>%%currentdate%%</td><td>Current date</td></tr>
					<tr><td>%%currenttime%%</td><td>Current time</td></tr>
					<tr><td>%%author_bio%%</td><td>Author bio, meta desc only</td></tr>
				</tbody>
			</table>
		</div>

	</div>
</div>
