var attachmentCache = require( './cache.attachments.js' )
var getAttachmentContent = require( './getAttachmentContent' )

var Image = function( fields ) {
	var attachment_ids = []

	fields = _.map( fields, function( field ) {
		if ( 'image' !== field.type ) {
			return field
		}

		field.content = ''

		var attachment_id = field.$el.find( 'input[type=hidden]' ).val()

		attachment_ids.push( attachment_id )
		if ( attachmentCache.get( attachment_id, 'attachment' ) ) {
			field.content += getAttachmentContent( attachment_id )
		}

		return field
	})

	attachmentCache.refresh( attachment_ids )

	return fields
}

module.exports = Image
