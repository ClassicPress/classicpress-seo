<?php
/**
 * Link Suggestions
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Metaboxes
 */

$suggestions = cpseo()->admin->get_link_suggestions( get_post() );
if ( empty( $suggestions ) ) {
	echo $field->args( 'not_found' );
	return;
}

echo cpseo()->admin->get_link_suggestions_html( $suggestions );
