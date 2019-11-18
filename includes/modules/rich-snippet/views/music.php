<?php
/**
 * Metabox - Music Rich Snippet
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet
 */

$music = [ [ 'cpseo_rich_snippet', 'music' ] ];

$cmb->add_field([
	'id'      => 'cpseo_snippet_music_type',
	'type'    => 'radio_inline',
	'name'    => esc_html__( 'Type', 'cpseo' ),
	'options' => [
		'MusicGroup' => esc_html__( 'MusicGroup', 'cpseo' ),
		'MusicAlbum' => esc_html__( 'MusicAlbum', 'cpseo' ),
	],
	'classes' => 'cmb-row-33 nob',
	'default' => 'MusicGroup',
	'dep'     => $music,
]);
