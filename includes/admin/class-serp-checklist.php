<?php
/**
 * The SERP checklist functionality.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin
 */

namespace Classic_SEO\Admin;

use Classic_SEO\CMB2;
use Classic_SEO\Helper;
use Classic_SEO\Traits\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Serp_Checklist class.
 */
class Serp_Checklist {

	use Hooker;

	/**
	 * Locale strings.
	 *
	 * @var array
	 */
	private $locale = [];
	
	/**
	 * Hold post readability
	 *
	 * @var int
	 */
	public $readability = 0;

	/**
	 * Display SERP checklist.
	 */
	public function display() {
		$method = 'display_' . CMB2::current_object_type() . '_list';
		?>
		<div id="cpseo-serp-checklist" class="cpseo-serp-checklist">
			<?php
			foreach ( $this->get_groups() as $group => $state ) :
				$list = $this->$method();
				if ( isset( $list[ $group ] ) ) :
					?>
				<div id="cpseo-serp-group-<?php echo $group; ?>" class="cpseo-serp-group state-<?php echo $state; ?>" data-id="<?php echo $group; ?>">
					<div class="group-handle">
						<span class="group-status"></span>
						<h4><?php echo $this->get_heading( $group ); ?></h4>
						<button type="button" class="group-handlediv" aria-expanded="true"><span class="screen-reader-text"><?php printf( esc_html__( 'Toggle tests: %s', 'cpseo' ), $this->get_heading( $group ) ); // phpcs:ignore ?></span><span class="toggle-indicator" aria-hidden="true"></span></button>
					</div>
					<ul>
						<?php $this->print_list( $list[ $group ] ); ?>
					</ul>
				</div>
					<?php
				endif;
			endforeach;
			?>
		</div>
		<?php
		Helper::add_json( 'assessor', [ '__' => $this->locale ] );
	}

	/**
	 * Display SERP checklist for posts.
	 */
	private function display_post_list() {
		$tests = [
			'basic'               => [
				'keywordInTitle'           => [
					'ok'      => esc_html__( 'You\'re using Focus Keyword in the SEO Title.', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword does not appear in the SEO title.', 'cpseo' ),
					'empty'   => esc_html__( 'Add Focus Keyword to the SEO title.', 'cpseo' ),
					'tooltip' => esc_html__( 'Make sure the focus keyword appears in the SEO post title too.', 'cpseo' ),
					'score'   => 'en' === substr( get_locale(), 0, 2 ) ? 30 : 32,
				],
				'keywordInMetaDescription' => [
					'ok'      => esc_html__( 'Focus Keyword is used inside SEO Meta Description.', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword not found in your SEO Meta Description.', 'cpseo' ),
					'empty'   => esc_html__( 'Add Focus Keyword to your SEO Meta Description.', 'cpseo' ),
					'tooltip' => esc_html__( 'Make sure the focus keyword appears in the SEO description too.', 'cpseo' ),
					'score'   => 2,
				],
				'keywordInPermalink'       => [
					'ok'      => esc_html__( 'Focus Keyword is used in the URL.', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword not found in the URL.', 'cpseo' ),
					'empty'   => esc_html__( 'Use Focus Keyword in the URL.', 'cpseo' ),
					'tooltip' => esc_html__( 'Include the focus keyword in the slug (permalink) of this post.', 'cpseo' ),
					'score'   => 5,
				],
				'keywordIn10Content'       => [
					'ok'      => esc_html__( 'Focus Keyword appears in the first 10% of the content.', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword doesn\'t appear at the beginning of your content.', 'cpseo' ),
					'empty'   => esc_html__( 'Use Focus Keyword at the beginning of your content.', 'cpseo' ),
					'tooltip' => esc_html__( 'The first 10% of the content should contain the Focus Keyword preferably at the beginning.', 'cpseo' ),
					'score'   => 3,
				],
				'keywordInContent'         => [
					'ok'      => esc_html__( 'Focus Keyword found in the content.', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword doesn\'t appear in the content.', 'cpseo' ),
					'empty'   => esc_html__( 'Use Focus Keyword in the content.', 'cpseo' ),
					'tooltip' => esc_html__( 'It is recommended to make the focus keyword appear in the post content too.', 'cpseo' ),
					'score'   => 3,
				],
				'lengthContent'            => [
					'ok'      => esc_html__( 'Your content is {0} words long. Good job!', 'cpseo' ),
					'fail'    => esc_html__( 'Your content is {0} words long. Consider using at least 500 words.', 'cpseo' ),
					/* translators: link to kb article */
					'empty'   => sprintf( esc_html__( 'Aim to write a minimum of 500 words.', 'cpseo' ) ),
					'tooltip' => esc_html__( 'There is no hard and fast rule about how many words is best but research shows that longer content generally leads to better rankings.', 'cpseo' ),
					'score'   => 8,
				],
			],
			'advanced'            => [
				'lengthPermalink'      => [
					'ok'      => esc_html__( 'URL is {0} characters long.', 'cpseo' ),
					'fail'    => esc_html__( 'URL is {0} characters long. Considering shortening it.', 'cpseo' ),
					'empty'   => esc_html__( 'URL unavailable. Add a short URL.', 'cpseo' ),
					'tooltip' => esc_html__( 'Permalink should be at most 75 characters long.', 'cpseo' ),
					'score'   => 4,
				],
				'keywordInSubheadings' => [
					'ok'      => esc_html__( 'Focus Keyword found in the subheading(s).', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword not found in subheading(s) like H2, H3, H4, etc..', 'cpseo' ),
					'empty'   => esc_html__( 'Use Focus Keyword in subheading(s) like H2, H3, H4, etc..', 'cpseo' ),
					'tooltip' => esc_html__( 'It is recommended to add the focus keyword as part of one or more subheadings in the content.', 'cpseo' ),
					'score'   => 3,
				],
				'keywordInImageAlt'    => [
					'ok'      => esc_html__( 'Focus Keyword found in image alt attribute(s).', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword not found in image alt attribute(s).', 'cpseo' ),
					'empty'   => esc_html__( 'Add an image with your Focus Keyword as alt text.', 'cpseo' ),
					'gallery' => esc_html__( 'We detected a gallery in your content & assuming that you added Focus Keyword in alt in at least one of the gallery images.', 'cpseo' ),
					'tooltip' => esc_html__( 'It is recommended to add the focus keyword in the alt attribute of one or more images.', 'cpseo' ),
					'score'   => 2,
				],
				'linksHasExternals'    => [
					'ok'      => esc_html__( 'Good! You are linking to external resources.', 'cpseo' ),
					'fail'    => esc_html__( 'No outbound links were found. Link out to external resources.', 'cpseo' ),
					'empty'   => esc_html__( 'Link out to external resources.', 'cpseo' ),
					'tooltip' => esc_html__( 'It helps visitors read more about a topic and prevents pogosticking.', 'cpseo' ),
					'score'   => 4,
				],
				'linksNotAllExternals' => [
					'ok'      => esc_html__( 'At least one external link with DoFollow found in your content.', 'cpseo' ),
					'fail'    => esc_html__( 'We found {0} outbound links in your content and all of them are nofollow.', 'cpseo' ),
					'empty'   => esc_html__( 'Add DoFollow links pointing to external resources.', 'cpseo' ),
					'tooltip' => esc_html__( 'PageRank Sculpting no longer works. Your posts should have a mix of nofollow and DoFollow links.', 'cpseo' ),
					'score'   => 2,
				],
				'keywordDensity'       => [
					'ok'      => esc_html__( 'Keyword Density is {0}, the Focus Keyword and combination appears {1} times.', 'cpseo' ),
					'fail'    => esc_html__( 'Keyword Density is {0}, the Focus Keyword and combination appears {1} times.', 'cpseo' ),
					'empty'   => esc_html__( 'Keyword Density is {0}. Aim for around 1% Keyword Density.', 'cpseo' ),
					'tooltip' => esc_html__( 'There is no ideal keyword density percentage, but it should not be too high. The most important thing is to keep the copy natural.', 'cpseo' ),
					'score'   => 6,
				],
				'linksHasInternal'     => [
					'ok'      => esc_html__( 'You are linking to other resources on your website which is good.', 'cpseo' ),
					'fail'    => esc_html__( 'We couldn\'t find any internal links in your content.', 'cpseo' ),
					'empty'   => esc_html__( 'Add internal links in your content.', 'cpseo' ),
					'tooltip' => esc_html__( 'Internal links decrease your bounce rate and improve SEO.', 'cpseo' ),
					'score'   => 5,
				],
				'keywordNotUsed'       => [
					'ok'      => esc_html__( 'You haven\'t used this Focus Keyword before.', 'cpseo' ),
					/* translators: focus keyword link */
					'fail'    => sprintf( esc_html__( 'You have %s this Focus Keyword.', 'cpseo' ), '<a target="_blank" class="focus-keyword-link" href="' . admin_url( 'edit.php?focus_keyword=%focus_keyword%&post_type=%post_type%' ) . '">' . __( 'already used', 'cpseo' ) . '</a>' ),
					'empty'   => esc_html__( 'Set a Focus Keyword for this content.', 'cpseo' ),
					'looking' => esc_html__( 'We are searching in database.', 'cpseo' ),
					'score'   => 0,
				],
			],
			'title-readability'   => [
				'titleStartWithKeyword' => [
					'ok'      => esc_html__( 'Focus Keyword is used at the beginning of SEO title.', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword doesn\'t appear at the beginning of SEO title.', 'cpseo' ),
					'empty'   => esc_html__( 'Use the Focus Keyword near the beginning of SEO title.', 'cpseo' ),
					'tooltip' => esc_html__( 'The SEO page title should contain the Focus Keyword preferably at the beginning.', 'cpseo' ),
					'score'   => 3,
				],
				'titleSentiment'        => [
					'ok'      => esc_html__( 'Your title has a positive or a negative sentiment.', 'cpseo' ),
					'fail'    => sprintf( __( 'Your title doesn\'t contain a positive or a negative sentiment word.', 'cpseo' ) ),
					'empty'   => esc_html__( 'Titles with positive or negative sentiment work best for higher CTR.', 'cpseo' ),
					'tooltip' => esc_html__( 'Headlines with a strong emotional sentiment (positive or negative) tend to receive more clicks.', 'cpseo' ),
					'score'   => 1,
				],
				'titleHasPowerWords'    => [
					'ok'      => esc_html__( 'Your title contains {0} power word(s).', 'cpseo' ),
					/* translators: link to kb article */
					'fail'    => sprintf( esc_html__( 'Your title doesn\'t contain a %s. Add at least one.', 'cpseo' ), '<a href="https://sumo.com/stories/power-words" target="_blank">power word</a>' ),
					/* translators: link to kb article */
					'empty'   => sprintf( esc_html__( 'Add %s to your title to increase CTR.', 'cpseo' ), '<a href="https://sumo.com/stories/power-words" target="_blank">power words</a>' ),
					/* translators: link to registration screen */
					'tooltip' => esc_html__( 'Power Words are tried-and-true words that copywriters use to attract more clicks.', 'cpseo' ),
					'score'   => 1,
				],
				'titleHasNumber'        => [
					'ok'      => esc_html__( 'You are using a number in your SEO title.', 'cpseo' ),
					'fail'    => esc_html__( 'Your SEO title doesn\'t contain a number.', 'cpseo' ),
					'empty'   => esc_html__( 'Add a number to your title to improve CTR.', 'cpseo' ),
					'tooltip' => esc_html__( 'Headlines with numbers are 36% more likely to generate clicks, according to research by Conductor.', 'cpseo' ),
					'score'   => 1,
				],
			],
			'content-readability' => [
				'contentHasTOC'             => [
					/* translators: link to kb article */
					'ok'      => sprintf( __( 'You seem to be using a Table of Contents plugin to break-down your text.', 'cpseo' ) ),
					/* translators: link to kb article */
					'fail'    => sprintf( __( 'You don\'t seem to be using a Table of Contents plugin.', 'cpseo' ) ),
					'empty'   => esc_html__( 'Use Table of Content to break-down your text.', 'cpseo' ),
					'tooltip' => esc_html__( ' Table of Contents help break down content into smaller, digestible chunks. It makes reading easier which in turn results in better rankings.', 'cpseo' ),
					'score'   => 2,
				],
				'calculateFleschReading'    => [
					/* translators: Link to kb article */
					'ok'      => esc_html__( 'Your Flesch Readability score is {1} and is regarded as {0}', 'cpseo' ),
					'fail'    => esc_html__( 'Your Flesch Readability score is {1} and is regarded as {0}.', 'cpseo' ),
					'empty'   => esc_html__( 'Add some content to calculate Flesch Readability score.', 'cpseo' ),
					'tooltip' => esc_html__( 'Try to make shorter sentences, using less difficult words to improve readability.', 'cpseo' ),
					'score'   => 6,
				],
				'contentHasShortParagraphs' => [
					'ok'      => esc_html__( 'You are using short paragraphs.', 'cpseo' ),
					'fail'    => esc_html__( 'At least one paragraph is long. Consider using short paragraphs.', 'cpseo' ),
					'empty'   => esc_html__( 'Add short and concise paragraphs for better readability and UX.', 'cpseo' ),
					'tooltip' => esc_html__( 'Short paragraphs are easier to read and more pleasing to the eye. Long paragraphs scare the visitor, and they might result to SERPs looking for better readable content.', 'cpseo' ),
					'score'   => 3,
				],
				'contentHasAssets'          => [
					'ok'      => esc_html__( 'Your content contains images and/or video(s).', 'cpseo' ),
					'fail'    => esc_html__( 'You are not using rich media like images or videos.', 'cpseo' ),
					'empty'   => esc_html__( 'Add a few images and/or videos to make your content appealing.', 'cpseo' ),
					'tooltip' => esc_html__( 'Content with images and/or video feels more inviting to users. It also helps supplement your textual content.', 'cpseo' ),
					'score'   => 6,
				],
			],
		];
		$tests = $this->do_filter( 'researches/tests', $tests, 'post' );
		Helper::add_json(
			'assessor',
			[
				'researchesTests' => array_merge( $tests['basic'], $tests['advanced'], $tests['title-readability'], $tests['content-readability'] ),
				'imgAlt'          => Helper::get_settings( 'general.cpseo_add_img_alt' ) && Helper::get_settings( 'general.cpseo_img_alt_format' ) ? trim( Helper::get_settings( 'general.cpseo_img_alt_format' ) ) : false,
				'defaultScore'    => $this->get_default_score( $tests, 'post' ),
			]
		);

		return $tests;
	}

	/**
	 * Display SERP checklist for terms.
	 */
	private function display_term_list() {
		$tests = [
			'basic'    => [
				'keywordInTitle'           => [
					'ok'      => esc_html__( 'You\'re using Focus Keyword in the SEO Title.', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword does not appear in the SEO title.', 'cpseo' ),
					'empty'   => esc_html__( 'Add Focus Keyword to the SEO title.', 'cpseo' ),
					'tooltip' => esc_html__( 'Make sure the focus keyword appears in the SEO Term too.', 'cpseo' ),
					'score'   => 40,
				],
				'keywordInMetaDescription' => [
					'ok'      => esc_html__( 'Focus Keyword is used inside SEO Meta Description.', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword not found in your SEO Meta Description.', 'cpseo' ),
					'empty'   => esc_html__( 'Add Focus Keyword to your SEO Meta Description.', 'cpseo' ),
					'tooltip' => esc_html__( 'Make sure the focus keyword appears in the SEO description too.', 'cpseo' ),
					'score'   => 20,
				],
				'keywordInPermalink'       => [
					'ok'      => esc_html__( 'Focus Keyword is used in the URL.', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword not found in the URL.', 'cpseo' ),
					'empty'   => esc_html__( 'Use Focus Keyword in the URL.', 'cpseo' ),
					'tooltip' => esc_html__( 'Include the focus keyword in the slug (permalink) of this term.', 'cpseo' ),
					'score'   => 30,
				],
			],
			'advanced' => [
				'titleStartWithKeyword' => [
					'ok'      => esc_html__( 'Focus Keyword is used at the beginning of SEO title.', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword doesn\'t appear at the beginning of SEO title.', 'cpseo' ),
					'empty'   => esc_html__( 'Use the Focus Keyword near the beginning of SEO title.', 'cpseo' ),
					'tooltip' => esc_html__( 'The SEO Term title should contain the Focus Keyword preferably at the beginning.', 'cpseo' ),
					'score'   => 10,
				],
				'keywordNotUsed'        => [
					'ok'    => esc_html__( 'You haven\'t used this Focus Keyword before.', 'cpseo' ),
					'fail'  => esc_html__( 'You have already used this Focus Keyword.', 'cpseo' ),
					'empty' => esc_html__( 'Set a Focus Keyword for this content.', 'cpseo' ),
					'score' => 0,
				],
			],
		];

		$tests = $this->do_filter( 'researches/tests', $tests, 'term' );
		Helper::add_json(
			'assessor',
			[
				'researchesTests' => array_merge( $tests['basic'], $tests['advanced'] ),
				'defaultScore'    => $this->get_default_score( $tests, 'term' ),
			]
		);

		return $tests;
	}

	/**
	 * Display SERP checklist for users.
	 */
	private function display_user_list() {
		$tests = [
			'basic'    => [
				'keywordInTitle'           => [
					'ok'      => esc_html__( 'You\'re using Focus Keyword in the SEO Title.', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword does not appear in the SEO title.', 'cpseo' ),
					'empty'   => esc_html__( 'Add Focus Keyword to the SEO title.', 'cpseo' ),
					'tooltip' => esc_html__( 'Make sure the focus keyword appears in the SEO Author too.', 'cpseo' ),
					'score'   => 40,
				],
				'keywordInMetaDescription' => [
					'ok'      => esc_html__( 'Focus Keyword is used inside SEO Meta Description.', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword not found in your SEO Meta Description.', 'cpseo' ),
					'empty'   => esc_html__( 'Add Focus Keyword to your SEO Meta Description.', 'cpseo' ),
					'tooltip' => esc_html__( 'Make sure the focus keyword appears in the SEO description too.', 'cpseo' ),
					'score'   => 20,
				],
				'keywordInPermalink'       => [
					'ok'      => esc_html__( 'Focus Keyword is used in the URL.', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword not found in the URL.', 'cpseo' ),
					'empty'   => esc_html__( 'Use Focus Keyword in the URL.', 'cpseo' ),
					'tooltip' => esc_html__( 'Include the focus keyword in the slug (permalink) of this author.', 'cpseo' ),
					'score'   => 30,
				],
			],
			'advanced' => [
				'titleStartWithKeyword' => [
					'ok'      => esc_html__( 'Focus Keyword is used at the beginning of SEO title.', 'cpseo' ),
					'fail'    => esc_html__( 'Focus Keyword doesn\'t appear at the beginning of SEO title.', 'cpseo' ),
					'empty'   => esc_html__( 'Use the Focus Keyword near the beginning of SEO title.', 'cpseo' ),
					'tooltip' => esc_html__( 'The SEO Author title should contain the Focus Keyword preferably at the beginning.', 'cpseo' ),
					'score'   => 10,
				],
				'keywordNotUsed'        => [
					'ok'    => esc_html__( 'You haven\'t used this Focus Keyword before.', 'cpseo' ),
					'fail'  => esc_html__( 'You have already used this Focus Keyword.', 'cpseo' ),
					'empty' => esc_html__( 'Set a Focus Keyword for this content.', 'cpseo' ),
					'score' => 0,
				],
			],
		];

		$tests = $this->do_filter( 'researches/tests', $tests, 'user' );
		Helper::add_json(
			'assessor',
			[
				'researchesTests' => array_merge( $tests['basic'], $tests['advanced'] ),
				'defaultScore'    => $this->get_default_score( $tests, 'user' ),
			]
		);

		return $tests;
	}

	/**
	 * Get default score.
	 *
	 * @param array  $tests  Array of tests.
	 * @param string $object Object type.
	 *
	 * @return int Default score.
	 */
	private function get_default_score( $tests, $object ) {
		$all_tests = array_merge( $tests['basic'], $tests['advanced'] );
		if ( 'post' === $object ) {
			$all_tests = array_merge( $all_tests, $tests['title-readability'], $tests['content-readability'] );
		}
		return 100 - array_sum( array_column( $all_tests, 'score' ) );
	}

	/**
	 * Print checklist.
	 *
	 * @param array $list Array of checklist to print.
	 */
	private function print_list( $list ) {
		$primary = [
			'keywordInTitle',
			'keywordInMetaDescription',
			'keywordInPermalink',
			'keywordIn10Content',
			'keywordInImageAlt',
			'keywordNotUsed',
			'titleStartWithKeyword',
		];

		foreach ( $list as $id => $item ) :
			if ( $this->is_invalid( $id ) ) {
				continue;
			}

			$this->add_locale( $id, $item );
			?>

			<li class="seo-check-<?php echo $id; ?> test-fail<?php echo in_array( $id, $primary, true ) ? ' is-primary' : ''; ?>">
				<span class="seo-check-text"><?php echo str_replace( [ '{0}', '{1}' ], '_', $item['fail'] ); ?></span>
				<?php echo isset( $item['tooltip'] ) ? Admin_Helper::get_tooltip( $item['tooltip'] ) : ''; ?>
			</li>
			<?php
		endforeach;
	}

	/**
	 * If invalid to print.
	 *
	 * @param string $id List id.
	 *
	 * @return bool
	 */
	private function is_invalid( $id ) {
		return 'en' !== substr( get_locale(), 0, 2 ) && in_array( $id, [ 'titleSentiment', 'titleHasPowerWords' ], true );
	}

	/**
	 * Add locale.
	 *
	 * @param string $id   List id.
	 * @param array  $item List info.
	 */
	private function add_locale( $id, $item ) {
		foreach ( [ 'ok', 'fail', 'empty', 'looking', 'gallery' ] as $key ) {
			if ( ! empty( $item[ $key ] ) ) {
				$this->locale[ $id . '.' . $key ] = $item[ $key ];
			}
		}
	}

	/**
	 * Get heading of the checklist group.
	 *
	 * @param  string $id ID of the checklist section.
	 * @return string
	 */
	private function get_heading( $id ) {
		$hash = [
			'basic'               => esc_html__( 'Basic SEO', 'cpseo' ),
			'advanced'            => esc_html__( 'Additional', 'cpseo' ),
			'title-readability'   => esc_html__( 'Title Readability', 'cpseo' ),
			'content-readability' => esc_html__( 'Content Readability', 'cpseo' ),
		];

		return isset( $hash[ $id ] ) ? $hash[ $id ] : esc_html__( 'Unkown', 'cpseo' );
	}

	/**
	 * Get checklist groups.
	 */
	private function get_groups() {
		$defaults = [
			'basic'               => 'open',
			'advanced'            => 'open',
			'title-readability'   => 'open',
			'content-readability' => 'open',
		];
		$groups   = get_user_meta( get_current_user_id(), 'cpseo_metabox_checklist_layout', true );

		return $groups ? array_merge( $defaults, $groups ) : $defaults;
	}
}
