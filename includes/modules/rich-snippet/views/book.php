<?php
/**
 * Metabox - Book Rich Snippet
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet
 */

$book_dep = [ [ 'cpseo_rich_snippet', 'book' ] ];

$cmb->add_field([
	'id'      => 'cpseo_snippet_book_rating',
	'type'    => 'text',
	'name'    => esc_html__( 'Rating', 'cpseo' ),
	'desc'    => esc_html__( 'Rating score of the book. Optional.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $book_dep,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_book_rating_min',
	'type'    => 'text',
	'name'    => esc_html__( 'Rating Minimum', 'cpseo' ),
	'desc'    => esc_html__( 'Rating minimum score of the book.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $book_dep,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_book_rating_max',
	'type'    => 'text',
	'name'    => esc_html__( 'Rating Maximum', 'cpseo' ),
	'desc'    => esc_html__( 'Rating maximum score of the book.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $book_dep,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_book_editions',
	'type'    => 'group',
	'name'    => esc_html__( 'Book Editions', 'cpseo' ),
	'desc'    => esc_html__( 'Either a specific edition of the written work, or the volume of the work.', 'cpseo' ),
	'options' => [
		'closed'        => true,
		'sortable'      => true,
		'add_button'    => esc_html__( 'Add New', 'cpseo' ),
		'group_title'   => esc_html__( 'Book Edition {#}', 'cpseo' ),
		'remove_button' => esc_html__( 'Remove', 'cpseo' ),
	],
	'classes' => 'cmb-group-fix-me nob',
	'dep'     => $book_dep,
	'fields'  => [
		[
			'id'   => 'name',
			'type' => 'text',
			'name' => esc_html__( 'Title', 'cpseo' ),
			'desc' => __( 'The title of the tome. Use for the title of the tome if it differs from the book.<br>*Optional when tome has the same title as the book.', 'cpseo' ),
		],

		[
			'id'   => 'book_edition',
			'type' => 'text',
			'name' => esc_html__( 'Edition', 'cpseo' ),
			'desc' => esc_html__( 'The edition of the book.', 'cpseo' ),
		],

		[
			'id'   => 'isbn',
			'type' => 'text',
			'name' => esc_html__( 'ISBN', 'cpseo' ),
			'desc' => esc_html__( 'The ISBN of the print book.', 'cpseo' ),
		],

		[
			'id'         => 'url',
			'type'       => 'text_url',
			'name'       => esc_html__( 'URL', 'cpseo' ),
			'desc'       => esc_html__( 'URL specific to this edition if one exists.', 'cpseo' ),
			'attributes' => [
				'data-rule-url' => 'true',
			],
			'classes'    => 'cpseo-validate-field',
		],

		[
			'id'   => 'author',
			'type' => 'text',
			'name' => esc_html__( 'Author(s)', 'cpseo' ),
			'desc' => __( 'The author(s) of the tome. Use if the author(s) of the tome differ from the related book. Provide one Person entity per author.<br>*Optional when the tome has the same set of authors as the book.', 'cpseo' ),
		],

		[
			'id'   => 'date_published',
			'type' => 'text_date',
			'name' => esc_html__( 'Date Published', 'cpseo' ),
			'desc' => esc_html__( 'Date of first publication of this tome.', 'cpseo' ),
		],

		[
			'id'      => 'book_format',
			'type'    => 'radio_inline',
			'name'    => esc_html__( 'Book Format', 'cpseo' ),
			'desc'    => esc_html__( 'The format of the book.', 'cpseo' ),
			'options' => [
				'EBook'     => esc_html__( 'EBook', 'cpseo' ),
				'Hardcover' => esc_html__( 'Hardcover', 'cpseo' ),
				'Paperback' => esc_html__( 'Paperback', 'cpseo' ),
				'AudioBook' => esc_html__( 'Audio Book', 'cpseo' ),
			],
			'default' => 'Hardcover',
		],
	],
]);
