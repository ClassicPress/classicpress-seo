<?php
/**
 * This code adds the Facebook metadata.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\OpenGraph
 */

namespace Classic_SEO\OpenGraph;

use DateInterval;
use Classic_SEO\Helper;
use Classic_SEO\Paper\Paper;
use Classic_SEO\Helpers\Str;
use Classic_SEO\Helpers\Conditional;

defined( 'ABSPATH' ) || exit;

/**
 * Facebook class.
 */
class Facebook extends OpenGraph {

	/**
	 * Network slug.
	 *
	 * @var string
	 */
	public $network = 'facebook';

	/**
	 * Metakey prefix.
	 *
	 * @var string
	 */
	public $prefix = 'facebook';

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->hooks();
		add_filter( 'jetpack_enable_open_graph', '__return_false' );
		parent::__construct();
	}

	/**
	 * Hooks
	 */
	private function hooks() {
		if ( isset( $GLOBALS['fb_ver'] ) || class_exists( 'Facebook_Loader', false ) ) {
			$this->filter( 'fb_meta_tags', 'facebook_filter', 10, 1 );
			return;
		}

		$this->filter( 'language_attributes', 'add_namespace', 15 );
		$this->action( 'cpseo/opengraph/facebook', 'locale', 1 );
		$this->action( 'cpseo/opengraph/facebook', 'type', 5 );
		$this->action( 'cpseo/opengraph/facebook', 'title', 10 );
		$this->action( 'cpseo/opengraph/facebook', 'description', 11 );
		$this->action( 'cpseo/opengraph/facebook', 'url', 12 );
		$this->action( 'cpseo/opengraph/facebook', 'site_name', 13 );
		$this->action( 'cpseo/opengraph/facebook', 'website', 14 );
		$this->action( 'cpseo/opengraph/facebook', 'site_owner', 20 );
		$this->action( 'cpseo/opengraph/facebook', 'image', 30 );

	}

	/**
	 * Filter the Facebook plugins metadata.
	 *
	 * @param  array $meta_tags The array to fix.
	 * @return array
	 */
	public function facebook_filter( $meta_tags ) {
		$meta_tags['http://ogp.me/ns#type']  = $this->type( false );
		$meta_tags['http://ogp.me/ns#title'] = $this->title( false );

		// Filter the locale too because the Facebook plugin locale code is not as good as ours.
		$meta_tags['http://ogp.me/ns#locale'] = $this->locale( false );

		$desc = $this->description( false );
		if ( ! empty( $desc ) ) {
			$meta_tags['http://ogp.me/ns#description'] = $desc;
		}

		return $meta_tags;
	}

	/**
	 * Adds prefix attributes to the <html> tag.
	 *
	 * @param  string $input The input namespace string.
	 * @return string
	 */
	public function add_namespace( $input ) {
		return $input . ' prefix="og: http://ogp.me/ns#"';
	}

	/**
	 * Output the locale, doing some conversions to make sure the proper Facebook locale is outputted.
	 *
	 * @see  http://www.facebook.com/translations/FacebookLocales.xml for the list of supported locales
	 * @link https://developers.facebook.com/docs/reference/opengraph/object-type/article/
	 *
	 * @param bool $echo Whether to echo or return the locale.
	 * @return string
	 */
	public function locale( $echo = true ) {
		$locale = get_locale();

		// Catch some weird locales served out by WP that are not easily doubled up.
		$fix_locales = [
			'ca' => 'ca_ES',
			'en' => 'en_US',
			'el' => 'el_GR',
			'et' => 'et_EE',
			'ja' => 'ja_JP',
			'sq' => 'sq_AL',
			'uk' => 'uk_UA',
			'vi' => 'vi_VN',
			'zh' => 'zh_CN',
		];

		if ( isset( $fix_locales[ $locale ] ) ) {
			$locale = $fix_locales[ $locale ];
		}

		// Convert locales like "es" to "es_ES", in case that works for the given locale (sometimes it does).
		if ( 2 === strlen( $locale ) ) {
			$locale = strtolower( $locale ) . '_' . strtoupper( $locale );
		}

		// These are the locales FB supports.
		$fb_valid_fb_locales = [
			'af_ZA', // Afrikaans.
			'ak_GH', // Akan.
			'am_ET', // Amharic.
			'ar_AR', // Arabic.
			'as_IN', // Assamese.
			'ay_BO', // Aymara.
			'az_AZ', // Azerbaijani.
			'be_BY', // Belarusian.
			'bg_BG', // Bulgarian.
			'bn_IN', // Bengali.
			'br_FR', // Breton.
			'bs_BA', // Bosnian.
			'ca_ES', // Catalan.
			'cb_IQ', // Sorani Kurdish.
			'ck_US', // Cherokee.
			'co_FR', // Corsican.
			'cs_CZ', // Czech.
			'cx_PH', // Cebuano.
			'cy_GB', // Welsh.
			'da_DK', // Danish.
			'de_DE', // German.
			'el_GR', // Greek.
			'en_GB', // English (UK).
			'en_IN', // English (India).
			'en_PI', // English (Pirate).
			'en_UD', // English (Upside Down).
			'en_US', // English (US).
			'eo_EO', // Esperanto.
			'es_CL', // Spanish (Chile).
			'es_CO', // Spanish (Colombia).
			'es_ES', // Spanish (Spain).
			'es_LA', // Spanish.
			'es_MX', // Spanish (Mexico).
			'es_VE', // Spanish (Venezuela).
			'et_EE', // Estonian.
			'eu_ES', // Basque.
			'fa_IR', // Persian.
			'fb_LT', // Leet Speak.
			'ff_NG', // Fulah.
			'fi_FI', // Finnish.
			'fo_FO', // Faroese.
			'fr_CA', // French (Canada).
			'fr_FR', // French (France).
			'fy_NL', // Frisian.
			'ga_IE', // Irish.
			'gl_ES', // Galician.
			'gn_PY', // Guarani.
			'gu_IN', // Gujarati.
			'gx_GR', // Classical Greek.
			'ha_NG', // Hausa.
			'he_IL', // Hebrew.
			'hi_IN', // Hindi.
			'hr_HR', // Croatian.
			'hu_HU', // Hungarian.
			'hy_AM', // Armenian.
			'id_ID', // Indonesian.
			'ig_NG', // Igbo.
			'is_IS', // Icelandic.
			'it_IT', // Italian.
			'ja_JP', // Japanese.
			'ja_KS', // Japanese (Kansai).
			'jv_ID', // Javanese.
			'ka_GE', // Georgian.
			'kk_KZ', // Kazakh.
			'km_KH', // Khmer.
			'kn_IN', // Kannada.
			'ko_KR', // Korean.
			'ku_TR', // Kurdish (Kurmanji).
			'ky_KG', // Kyrgyz.
			'la_VA', // Latin.
			'lg_UG', // Ganda.
			'li_NL', // Limburgish.
			'ln_CD', // Lingala.
			'lo_LA', // Lao.
			'lt_LT', // Lithuanian.
			'lv_LV', // Latvian.
			'mg_MG', // Malagasy.
			'mi_NZ', // Maori.
			'mk_MK', // Macedonian.
			'ml_IN', // Malayalam.
			'mn_MN', // Mongolian.
			'mr_IN', // Marathi.
			'ms_MY', // Malay.
			'mt_MT', // Maltese.
			'my_MM', // Burmese.
			'nb_NO', // Norwegian (bokmal).
			'nd_ZW', // Ndebele.
			'ne_NP', // Nepali.
			'nl_BE', // Dutch (Belgie).
			'nl_NL', // Dutch.
			'nn_NO', // Norwegian (nynorsk).
			'ny_MW', // Chewa.
			'or_IN', // Oriya.
			'pa_IN', // Punjabi.
			'pl_PL', // Polish.
			'ps_AF', // Pashto.
			'pt_BR', // Portuguese (Brazil).
			'pt_PT', // Portuguese (Portugal).
			'qu_PE', // Quechua.
			'rm_CH', // Romansh.
			'ro_RO', // Romanian.
			'ru_RU', // Russian.
			'rw_RW', // Kinyarwanda.
			'sa_IN', // Sanskrit.
			'sc_IT', // Sardinian.
			'se_NO', // Northern Sami.
			'si_LK', // Sinhala.
			'sk_SK', // Slovak.
			'sl_SI', // Slovenian.
			'sn_ZW', // Shona.
			'so_SO', // Somali.
			'sq_AL', // Albanian.
			'sr_RS', // Serbian.
			'sv_SE', // Swedish.
			'sw_KE', // Swahili.
			'sy_SY', // Syriac.
			'sz_PL', // Silesian.
			'ta_IN', // Tamil.
			'te_IN', // Telugu.
			'tg_TJ', // Tajik.
			'th_TH', // Thai.
			'tk_TM', // Turkmen.
			'tl_PH', // Filipino.
			'tl_ST', // Klingon.
			'tr_TR', // Turkish.
			'tt_RU', // Tatar.
			'tz_MA', // Tamazight.
			'uk_UA', // Ukrainian.
			'ur_PK', // Urdu.
			'uz_UZ', // Uzbek.
			'vi_VN', // Vietnamese.
			'wo_SN', // Wolof.
			'xh_ZA', // Xhosa.
			'yi_DE', // Yiddish.
			'yo_NG', // Yoruba.
			'zh_CN', // Simplified Chinese (China).
			'zh_HK', // Traditional Chinese (Hong Kong).
			'zh_TW', // Traditional Chinese (Taiwan).
			'zu_ZA', // Zulu.
			'zz_TR', // Zazaki.
		];

		// Check to see if the locale is a valid FB one, if not, use en_US as a fallback.
		if ( ! in_array( $locale, $fb_valid_fb_locales, true ) ) {
			$locale = strtolower( substr( $locale, 0, 2 ) ) . '_' . strtoupper( substr( $locale, 0, 2 ) );
			if ( ! in_array( $locale, $fb_valid_fb_locales, true ) ) {
				$locale = 'en_US';
			}
		}

		if ( $echo ) {
			$this->tag( 'og:locale', $locale );
		}

		return $locale;
	}

	/**
	 * Output the OpenGraph type.
	 *
	 * @link https://developers.facebook.com/docs/reference/opengraph/object-type/object/
	 *
	 * @param bool $echo Whether to echo or return the type.
	 * @return string
	 */
	public function type( $echo = true ) {

		// We use "object" for archives etc. as article doesn't apply there.
		$type = 'object';

		if ( is_front_page() || is_home() ) {
			$type = 'website';
		} elseif ( is_singular() ) {
			$type = Conditional::is_woocommerce_active() && is_product() ? 'product' : 'article';
			if ( in_array( $this->schema, [ 'video', 'product', 'local' ], true ) ) {
				$type = $this->schema;
				if ( ! is_front_page() ) {
					$this->action( 'cpseo/opengraph/facebook', $this->schema, 30 );
				}
			}

			if ( 'article' === $type && ! is_front_page() ) {
				$this->action( 'cpseo/opengraph/facebook', 'article_author', 15 );
				$this->action( 'cpseo/opengraph/facebook', 'tags', 16 );
				$this->action( 'cpseo/opengraph/facebook', 'category', 17 );
			}
			$this->action( 'cpseo/opengraph/facebook', 'publish_date', 19 );
		}

		/**
		 * Allow changing the OpenGraph type of the page.
		 *
		 * @param string $type The OpenGraph type string.
		 */
		$type = $this->do_filter( 'opengraph/type', $type );

		if ( Str::is_non_empty( $type ) && $echo ) {
			$this->tag( 'og:type', $type );
		}

		return $type;
	}

	/**
	 * Outputs the SEO title as OpenGraph title.
	 *
	 * @link https://developers.facebook.com/docs/reference/opengraph/object-type/article/
	 *
	 * @param bool $echo Whether or not to echo the output.
	 * @return string
	 */
	public function title( $echo = true ) {
		$title = trim( $this->get_title() );
		if ( $echo ) {
			$this->tag( 'og:title', $title );
		}

		return $title;
	}

	/**
	 * Output the OpenGraph description, specific OG description first, if not, grab the meta description.
	 *
	 * @param bool $echo Whether to echo or return the description.
	 * @return string
	 */
	public function description( $echo = true ) {
		$desc = trim( $this->get_description() );
		if ( $echo ) {
			$this->tag( 'og:description', $desc );
		}

		return $desc;
	}

	/**
	 * Outputs the canonical URL as OpenGraph URL, which consolidates likes and shares.
	 *
	 * @link https://developers.facebook.com/docs/reference/opengraph/object-type/article/
	 */
	public function url() {
		$url = $this->do_filter( 'opengraph/url', esc_url( Paper::get()->get_canonical() ) );
		$this->tag( 'og:url', $url );
	}

	/**
	 * Output the site name straight from the blog info.
	 */
	public function site_name() {
		$this->tag( 'og:site_name', get_bloginfo( 'name' ) );
	}

	/**
	 * Outputs the websites FB page.
	 *
	 * @link https://developers.facebook.com/blog/post/2013/06/19/platform-updates--new-open-graph-tags-for-media-publishers-and-more/
	 * @link https://developers.facebook.com/docs/reference/opengraph/object-type/article/
	 */
	public function website() {
		$site = Helper::get_settings( 'titles.cpseo_social_url_facebook' );
		if ( 'article' === $this->type( false ) && '' !== $site ) {
			$this->tag( 'article:publisher', $site );
		}
	}

	/**
	 * Outputs the site owner.
	 *
	 * @link https://developers.facebook.com/docs/reference/opengraph/object-type/article/
	 */
	public function site_owner() {
		$app_id = Helper::get_settings( 'titles.cpseo_facebook_app_id' );
		if ( 0 !== absint( $app_id ) ) {
			$this->tag( 'fb:app_id', $app_id );
			return;
		}

		$admins = Helper::get_settings( 'titles.cpseo_facebook_admin_id' );
		if ( '' !== trim( $admins ) ) {
			$this->tag( 'fb:admins', $admins );
			return;
		}
	}

	/**
	 * Create new Image class and get the images to set the og:image.
	 *
	 * @param string|bool $image Optional. Image URL.
	 */
	public function image( $image = false ) {
		$images = new Image( $image, $this );
		$images->show();
	}

	/**
	 * Outputs the authors FB page.
	 *
	 * @link https://developers.facebook.com/blog/post/2013/06/19/platform-updates--new-open-graph-tags-for-media-publishers-and-more/
	 * @link https://developers.facebook.com/docs/reference/opengraph/object-type/article/
	 */
	public function article_author() {
		$author = Helper::get_user_meta( 'facebook_author', $GLOBALS['post']->post_author );
		if ( ! $author && ! $author = get_user_meta( $GLOBALS['post']->post_author, 'facebook', true ) ) { // phpcs:ignore
			$author = Helper::get_settings( 'titles.cpseo_facebook_author_urls' );
		}
		$this->tag( 'article:author', $author );
	}

	/**
	 * Output the article tags as article:tag tags.
	 */
	public function tags() {
		$tags = get_the_tags();
		if ( is_wp_error( $tags ) || empty( $tags ) ) {
			return;
		}

		foreach ( $tags as $tag ) {
			$this->tag( 'article:tag', $tag->name );
		}
	}

	/**
	 * Output the article category as an article:section tag.
	 */
	public function category() {
		$post = get_post();
		if ( ! $post ) {
			return false;
		}

		$terms = get_the_category();
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return;
		}

		// We can only show one section here, so we take the first one.
		$this->tag( 'article:section', $terms[0]->name );
	}

	/**
	 * Output the article publish and last modification date.
	 */
	public function publish_date() {
		$post = get_post();
		$pub  = mysql2date( DATE_W3C, $post->post_date_gmt, false );
		$mod  = mysql2date( DATE_W3C, $post->post_modified_gmt, false );

		if ( 'article' === $this->schema ) {
			$this->tag( 'article:published_time', $pub );
			if ( $mod !== $pub ) {
				$this->tag( 'article:modified_time', $mod );
			}
		}
		if ( $mod !== $pub ) {
			$this->tag( 'og:updated_time', $mod );
		}
	}

	/**
	 * Output product tags
	 */
	public function product() {
		if ( ! class_exists( 'WooCommerce' ) || 'product' !== get_post_type() ) {
			$this->tag( 'product:brand', Helper::get_post_meta( 'snippet_product_brand' ) );
			$this->tag( 'product:price:amount', Helper::get_post_meta( 'snippet_product_price' ) );
			$this->tag( 'product:price:currency', Helper::get_post_meta( 'snippet_product_currency' ) );
			if ( Helper::get_post_meta( 'snippet_product_instock', false ) ) {
				$this->tag( 'product:availability', 'instock' );
			}
		}
	}

	/**
	 * Output local info
	 */
	public function local() {
		$this->tag( 'og:url', Helper::get_post_meta( 'snippet_local_url' ) );

		if ( $geo = Helper::get_post_meta( 'snippet_local_geo' ) ) { // phpcs:ignore
			$parts = explode( ' ', $geo );
			if ( count( $parts ) > 1 ) {
				$this->tag( 'place:location:latitude', $parts[0] );
				$this->tag( 'place:location:longitude', $parts[1] );
			}
		}
	}

	/**
	 * Output video tags
	 */
	public function video() {
		$this->tag( 'og:video', Helper::get_post_meta( 'snippet_video_url' ) );
		if ( $duration = Helper::get_formatted_duration( Helper::get_post_meta( 'snippet_video_duration' ) ) ) { // phpcs:ignore
			$this->tag( 'video:duration', $this->duration_to_seconds( $duration ) );
		}
	}

	/**
	 * Helper function to convert ISO 8601 duration to seconds.
	 * For example "1H12M24S" becomes 5064.
	 *
	 * @param string $iso8601 Duration which need to be converted to seconds.
	 * @return int
	 */
	public function duration_to_seconds( $iso8601 ) {
		$interval = new DateInterval( $iso8601 );

		return array_sum([
			$interval->d * DAY_IN_SECONDS,
			$interval->h * HOUR_IN_SECONDS,
			$interval->i * MINUTE_IN_SECONDS,
			$interval->s,
		]);
	}
}
