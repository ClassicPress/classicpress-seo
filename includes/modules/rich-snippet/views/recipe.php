<?php
/**
 * Metabox - Recipe Rich Snippet
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet
 */

$recipe = [ [ 'cpseo_rich_snippet', 'recipe' ] ];

$cmb->add_field([
	'id'      => 'cpseo_snippet_recipe_type',
	'type'    => 'text',
	'name'    => esc_html__( 'Type', 'cpseo' ),
	'desc'    => esc_html__( 'Type of dish, for example "appetizer", or "dessert".', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $recipe,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_recipe_cuisine',
	'type'    => 'text',
	'name'    => esc_html__( 'Cuisine', 'cpseo' ),
	'desc'    => esc_html__( 'The cuisine of the recipe (for example, French or Ethiopian).', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $recipe,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_recipe_keywords',
	'type'    => 'text',
	'name'    => esc_html__( 'Keywords', 'cpseo' ),
	'desc'    => esc_html__( 'Other terms for your recipe such as the season, the holiday, or other descriptors. Separate multiple entries with commas.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $recipe,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_recipe_yield',
	'type'    => 'text',
	'name'    => esc_html__( 'Recipe Yield', 'cpseo' ),
	'desc'    => esc_html__( 'Quantity produced by the recipe, for example "4 servings"', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $recipe,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_recipe_calories',
	'type'    => 'text',
	'name'    => esc_html__( 'Calories', 'cpseo' ),
	'desc'    => esc_html__( 'The number of calories in the recipe. Optional.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $recipe,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_recipe_preptime',
	'type'       => 'text',
	'name'       => esc_html__( 'Preparation Time', 'cpseo' ),
	'desc'       => esc_html__( ' ISO 8601 duration format. Example: 1H30M', 'cpseo' ),
	'classes'    => 'cmb-row-33 cpseo-validate-field',
	'attributes' => [
		'data-rule-regex'       => 'true',
		'data-validate-pattern' => '^([0-9]+[A-Z])+$',
		'data-msg-regex'        => esc_html__( 'Please use the correct format. Example: 1H30M', 'cpseo' ),
	],
	'dep'        => $recipe,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_recipe_cooktime',
	'type'       => 'text',
	'name'       => esc_html__( 'Cooking Time', 'cpseo' ),
	'desc'       => esc_html__( ' ISO 8601 duration format. Example: 1H30M', 'cpseo' ),
	'classes'    => 'cmb-row-33 cpseo-validate-field',
	'attributes' => [
		'data-rule-regex'       => 'true',
		'data-validate-pattern' => '^([0-9]+[A-Z])+$',
		'data-msg-regex'        => esc_html__( 'Please use the correct format. Example: 1H30M', 'cpseo' ),
	],
	'dep'        => $recipe,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_recipe_totaltime',
	'type'       => 'text',
	'name'       => esc_html__( 'Total Time', 'cpseo' ),
	'desc'       => esc_html__( ' ISO 8601 duration format. Example: 1H30M', 'cpseo' ),
	'classes'    => 'cmb-row-33 cpseo-validate-field',
	'attributes' => [
		'data-rule-regex'       => 'true',
		'data-validate-pattern' => '^([0-9]+[A-Z])+$',
		'data-msg-regex'        => esc_html__( 'Please use the correct format. Example: 1H30M', 'cpseo' ),
	],
	'dep'        => $recipe,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_recipe_rating',
	'type'    => 'text',
	'name'    => esc_html__( 'Rating', 'cpseo' ),
	'desc'    => esc_html__( 'Rating score of the recipe. Optional.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $recipe,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_recipe_rating_min',
	'type'    => 'text',
	'name'    => esc_html__( 'Rating Minimum', 'cpseo' ),
	'desc'    => esc_html__( 'Rating minimum score of the recipe.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $recipe,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_recipe_rating_max',
	'type'    => 'text',
	'name'    => esc_html__( 'Rating Maximum', 'cpseo' ),
	'desc'    => esc_html__( 'Rating maximum score of the recipe.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $recipe,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_recipe_video',
	'type'       => 'text_url',
	'name'       => esc_html__( 'Recipe Video', 'cpseo' ),
	'desc'       => esc_html__( 'A recipe video URL. Optional.', 'cpseo' ),
	'classes'    => 'cmb-row-33 cpseo-validate-field',
	'attributes' => [
		'data-rule-url' => true,
	],
	'dep'        => $recipe,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_recipe_video_thumbnail',
	'type'       => 'text_url',
	'name'       => esc_html__( 'Recipe Video Thumbnail', 'cpseo' ),
	'desc'       => esc_html__( 'A recipe video thumbnail URL.', 'cpseo' ),
	'classes'    => 'cmb-row-33 cpseo-validate-field',
	'attributes' => [
		'data-rule-url' => true,
	],
	'dep'        => $recipe,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_recipe_video_name',
	'type'    => 'text',
	'name'    => esc_html__( 'Recipe Video Name', 'cpseo' ),
	'desc'    => esc_html__( 'A recipe video Name.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $recipe,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_recipe_video_date',
	'type'    => 'text_date',
	'name'    => esc_html__( 'Video Upload Date', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $recipe,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_recipe_video_description',
	'type'       => 'textarea',
	'name'       => esc_html__( 'Recipe Video Description', 'cpseo' ),
	'desc'       => esc_html__( 'A recipe video Description.', 'cpseo' ),
	'classes'    => 'cmb-row-50',
	'attributes' => [
		'rows'            => 4,
		'data-autoresize' => true,
	],
	'dep'        => $recipe,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_recipe_ingredients',
	'type'       => 'textarea',
	'name'       => esc_html__( 'Recipe Ingredients', 'cpseo' ),
	'desc'       => esc_html__( 'Recipe ingredients, add one item per line', 'cpseo' ),
	'attributes' => [
		'rows'            => 4,
		'data-autoresize' => true,
	],
	'classes'    => 'cmb-row-50',
	'dep'        => $recipe,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_recipe_instruction_type',
	'type'    => 'radio_inline',
	'name'    => esc_html__( 'Instruction Type', 'cpseo' ),
	'options' => [
		'SingleField'  => esc_html__( 'Single Field', 'cpseo' ),
		'HowToStep'    => esc_html__( 'How To Step', 'cpseo' ),
		'HowToSection' => esc_html__( 'How To Section', 'cpseo' ),
	],
	'classes' => 'recipe-instruction-type',
	'default' => 'SingleField',
	'dep'     => $recipe,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_recipe_instruction_name',
	'type'    => 'text',
	'name'    => esc_html__( 'Recipe Instruction Name', 'cpseo' ),
	'desc'    => esc_html__( 'Instruction name of the recipe.', 'cpseo' ),
	'classes' => 'nob',
	'dep'     => [
		'relation' => 'and',
		[ 'cpseo_rich_snippet', 'recipe' ],
		[ 'cpseo_snippet_recipe_instruction_type', 'HowToStep' ],
	],
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_recipe_single_instructions',
	'type'       => 'textarea',
	'name'       => esc_html__( 'Recipe Instructions', 'cpseo' ),
	'attributes' => [
		'rows'            => 4,
		'data-autoresize' => true,
	],
	'classes'    => 'nob',
	'dep'        => [
		'relation' => 'and',
		[ 'cpseo_rich_snippet', 'recipe' ],
		[ 'cpseo_snippet_recipe_instruction_type', 'HowToStep,SingleField' ],
	],
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_recipe_instructions',
	'type'    => 'group',
	'name'    => esc_html__( 'Recipe Instructions', 'cpseo' ),
	'desc'    => esc_html__( 'Steps to take, add one instruction per line', 'cpseo' ),
	'options' => [
		'closed'        => true,
		'sortable'      => true,
		'add_button'    => esc_html__( 'Add New Instruction', 'cpseo' ),
		'group_title'   => esc_html__( 'Instruction {#}', 'cpseo' ),
		'remove_button' => esc_html__( 'Remove', 'cpseo' ),
	],
	'classes' => 'cmb-group-fix-me nob',
	'dep'     => [
		'relation' => 'and',
		[ 'cpseo_rich_snippet', 'recipe' ],
		[ 'cpseo_snippet_recipe_instruction_type', 'HowToSection' ],
	],
	'fields'  => [
		[
			'id'   => 'name',
			'type' => 'text',
			'name' => esc_html__( 'Name', 'cpseo' ),
			'desc' => esc_html__( 'Instruction name of the recipe.', 'cpseo' ),
		],
		[
			'id'         => 'text',
			'type'       => 'textarea',
			'name'       => esc_html__( 'Text', 'cpseo' ),
			'attributes' => [
				'rows'            => 4,
				'data-autoresize' => true,
			],
			'desc'       => esc_html__( 'Steps to take, add one instruction per line', 'cpseo' ),
		],
	],
]);
