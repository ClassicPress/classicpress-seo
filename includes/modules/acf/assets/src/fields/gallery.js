var attachmentCache = require( './cache.attachments.js' )
var getAttachmentContent = require( './getAttachmentContent' )

var Gallery = function( fields ) {
	var attachment_ids = []

	fields = _.map( fields, function( field ) {
		if ( 'gallery' !== field.type ) {
			return field
		}

		field.content = ''

		field.$el.find( '.acf-gallery-attachment input[type=hidden]' ).each( function() {
			var attachment_id = jQuery( this ).val()
			attachment_ids.push( attachment_id )
			field.content += new getAttachmentContent( attachment_id )
		})

		return field
	})

	attachmentCache.refresh( attachment_ids )

	return fields
}

module.exports = Gallery
