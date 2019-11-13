/*global tinyMCE*/

/**
 * Check if is TinyMCEAvailable
 *
 * @param {string} editorID TinyMCE identifier to look up.
 *
 * @return {boolean} True if an editor exists for the supplied ID.
 */
const isTinyMCEAvailable = function( editorID ) {
	if (
		'undefined' === typeof tinyMCE ||
		'undefined' === typeof tinyMCE.editors ||
		0 === tinyMCE.editors.length ||
		null === tinyMCE.get( editorID ) ||
		tinyMCE.get( editorID ).isHidden()
	) {
		return false
	}

	return true
}

/**
 * Get content from the TinyMCE editor.
 *
 * @param {Object} field Field to get the content for.
 *
 * @return {string} The content of the field.
 */
const getContentTinyMCE = function( field ) {
	const textarea = field.$el.find( 'textarea' )[ 0 ]
	const editorID = textarea.id
	let val = textarea.value

	if ( isTinyMCEAvailable( editorID ) ) {
		val = ( tinyMCE.get( editorID ) && tinyMCE.get( editorID ).getContent() ) || ''
	}

	return val
}

export default function( fields ) {
	fields = _.map( fields, function( field ) {
		if ( 'wysiwyg' !== field.type ) {
			return field
		}
		field.content = getContentTinyMCE( field )

		return field
	} )

	return fields
}
