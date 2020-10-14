<?php
/**
 * Social preview tab template.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Metaboxes
 */

use Classic_SEO\Helper;
use Classic_SEO\Admin\Admin_Helper;
use Classic_SEO\Helpers\Param;

global $post;

$thumbnail = has_post_thumbnail() ? absint( get_post_thumbnail_id() ) : '';

// Facebook Image.
$fb_thumbnail = '';
if ( Admin_Helper::is_post_edit() ) {
	$fb_thumbnail = get_post_meta( $post->ID, 'cpseo_facebook_image_id', true );
} elseif ( Admin_Helper::is_term_edit() ) {
	$term_id      = Param::request( 'tag_ID', 0, FILTER_VALIDATE_INT );
	$fb_thumbnail = get_term_meta( $term_id, 'cpseo_facebook_image_id', true );
} elseif ( Admin_Helper::is_user_edit() ) {
	global $user_id;
	$fb_thumbnail = get_user_meta( $user_id, 'cpseo_facebook_image_id', true );
}
$fb_thumbnail = $fb_thumbnail ? absint( $fb_thumbnail ) : $thumbnail;
if ( ! is_string( $fb_thumbnail ) ) {
	$image_src    = wp_get_attachment_image_src( $fb_thumbnail, 'full' );
	$fb_thumbnail = $image_src[0];
}

// Twitter Image.
$tw_thumbnail = '';
if ( Admin_Helper::is_post_edit() ) {
	$tw_thumbnail = get_post_meta( $post->ID, 'cpseo_twitter_image_id', true );
} elseif ( Admin_Helper::is_term_edit() ) {
	$term_id      = Param::request( 'tag_ID', 0, FILTER_VALIDATE_INT );
	$tw_thumbnail = get_term_meta( $term_id, 'cpseo_twitter_image_id', true );
} elseif ( Admin_Helper::is_user_edit() ) {
	global $user_id;
	$tw_thumbnail = get_user_meta( $user_id, 'cpseo_twitter_image_id', true );
}
$tw_thumbnail = $tw_thumbnail ? absint( $tw_thumbnail ) : $thumbnail;
if ( ! is_string( $tw_thumbnail ) ) {
	$image_src    = wp_get_attachment_image_src( $tw_thumbnail, 'full' );
	$tw_thumbnail = $image_src[0];
}

// Publisher URL.
$publisher_url = str_replace( array( 'http://', 'https://' ), '', get_bloginfo( 'url' ) );
$publisher_url = explode( '/', $publisher_url );
$publisher_url = isset( $publisher_url[0] ) ? $publisher_url[0] : '';

// Username, avatar & Name.
$name             = get_the_author_meta( 'display_name' );
$twitter_username = Helper::get_settings( 'titles.cpseo_twitter_author_names' );
$twitter_username = $twitter_username ? $twitter_username : esc_html( 'username' );
?>
<div id="setting-panel-container-social-tabs" class="cpseo-tabs">

	<div class="social-tabs-navigation-wrapper">
		<div class="cpseo-tabs-navigation cpseo-custom social-tabs-navigation wp-clearfix" data-active-class="tab-active">
			<a href="#setting-panel-social-facebook" class="preview-network tab-facebook"><span class="dashicons dashicons-facebook-alt"></span><?php esc_html_e( 'Facebook', 'cpseo' ); ?></a><a href="#setting-panel-social-twitter" class="preview-network tab-twitter"><span class="dashicons dashicons-twitter"></span><?php esc_html_e( 'Twitter', 'cpseo' ); ?></a>
		</div>
	</div>

	<div class="cpseo-social-preview">

		<a href="#" class="cpseo-social-preview-button"><strong data-facebook="<?php esc_html_e( 'Facebook Preview', 'cpseo' ); ?>" data-twitter="<?php esc_html_e( 'Twitter Preview', 'cpseo' ); ?>"></strong><span class="dashicons dashicons-arrow-down"></span></a>

		<div class="cpseo-social-preview-item">

			<div class="cpseo-social-preview-social-meta facebook-meta">
				<div class="social-profile-image"></div>
				<div class="social-name"><?php echo $name; ?></div>
				<div class="social-time"><span><?php esc_html_e( '2hrs', 'cpseo' ); ?></span><span class="dashicons dashicons-admin-site"></span></div>
			</div>

			<div class="cpseo-social-preview-social-meta twitter-meta">
				<div class="social-profile-image"></div>
				<div class="social-name"><?php echo $name; ?><span class="social-username">@<?php echo $twitter_username; ?></span><span class="social-time"><?php esc_html_e( '2h', 'cpseo' ); ?></span></div>
				<div class="social-text">The card for your website will look little something like this!</div>
			</div>

			<div class="cpseo-social-preview-item-wrapper">

				<div class="cpseo-social-preview-image">
					<?php the_post_thumbnail( 'full', 'id=cpseo_post_thumbnail' ); ?>
					<img class="facebook-thumbnail" src="<?php echo $fb_thumbnail; ?>" width="526" height="275" />
					<img class="twitter-thumbnail" src="<?php echo $tw_thumbnail; ?>" width="526" height="275" />
					<img src="" class="cpseo-social-preview-image-overlay">
				</div>

				<div class="cpseo-social-preview-caption">
					<h4 class="cpseo-social-preview-publisher facebook"><?php echo $publisher_url; ?></h4>
					<h3 class="cpseo-social-preview-title"></h3>
					<p class="cpseo-social-preview-description"></p>
					<h4 class="cpseo-social-preview-publisher twitter"><?php echo $publisher_url; ?></h4>
				</div>

			</div>
			<div class="error-msg">
				<?php
				printf(
					/* translators: Link to global title setting */
					__( 'Set your default image for Facebook & Twitter by adding <a href="%s" target="_blank">OpenGraph Thumbnail</a>', 'cpseo' ),
					Helper::get_admin_url( 'options-titles#setting-panel-global' )
				);
				?>
			</div>
		</div>

	</div>

	<div class="notice notice-alt notice-info info inline">
		<?php /* translators: link to title setting screen */ ?>
		<p><?php printf( wp_kses_post( __( 'Customize the title, description and images of your post used while sharing on Facebook and Twitter.', 'cpseo' ) ) ); ?></p>
	</div>
