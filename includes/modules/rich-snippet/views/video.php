<?php
/**
 * Metabox - Video Rich Snippet
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet
 */

$video = [ [ 'cpseo_rich_snippet', 'video' ] ];

$cmb->add_field([
	'id'         => 'cpseo_snippet_video_url',
	'type'       => 'text_url',
	'name'       => esc_html__( 'Content URL', 'cpseo' ),
	'desc'       => esc_html__( 'A URL pointing to the actual video media file.', 'cpseo' ),
	'classes'    => 'cmb-row-50 cpseo-validate-field',
	'attributes' => [
		'data-rule-url' => true,
	],
	'dep'        => $video,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_video_embed_url',
	'type'       => 'text_url',
	'name'       => esc_html__( 'Embed URL', 'cpseo' ),
	'desc'       => esc_html__( 'A URL pointing to the embeddable player for the video.', 'cpseo' ),
	'classes'    => 'cmb-row-50 cpseo-validate-field',
	'attributes' => [
		'data-rule-url' => true,
	],
	'dep'        => $video,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_video_duration',
	'type'       => 'text',
	'name'       => esc_html__( 'Duration', 'cpseo' ),
	'desc'       => esc_html__( ' ISO 8601 duration format. Example: 1H30M', 'cpseo' ),
	'classes'    => 'cmb-row-50 nob cpseo-validate-field',
	'attributes' => [
		'data-rule-regex'       => 'true',
		'data-validate-pattern' => '^([0-9]+[A-Z])+$',
		'data-msg-regex'        => esc_html__( 'Please use the correct format. Example: 1H30M', 'cpseo' ),
	],
	'dep'        => $video,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_video_views',
	'type'       => 'text',
	'name'       => esc_html__( 'Views', 'cpseo' ),
	'desc'       => esc_html__( 'Number of views', 'cpseo' ),
	'classes'    => 'cmb-row-50 nob',
	'dep'        => $video,
	'attributes' => [ 'type' => 'number' ],
]);
