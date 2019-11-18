import $ from 'jquery'
import attachmentCache from './attachmentCache'
import getAttachmentContent from './getAttachmentContent'

export default function( fields ) {
	const attachments = []

	fields = _.map( fields, function( field ) {
		if ( 'gallery' !== field.type ) {
			return field
		}

		field.content = ''

		field.$el.find( '.acf-gallery-attachment input[type=hidden]' ).each( function() {
			const attachmentID = $( this ).val()
			attachments.push( attachmentID )
			field.content += new getAttachmentContent( attachmentID )
		} )

		return field
	} )

	attachmentCache.refresh( attachments )

	return fields
}
