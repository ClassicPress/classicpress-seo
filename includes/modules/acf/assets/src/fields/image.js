import attachmentCache from './attachmentCache'
import getAttachmentContent from './getAttachmentContent'

export default function( fields ) {
	const attachments = []

	fields = _.map( fields, function( field ) {
		if ( 'image' !== field.type ) {
			return field
		}

		field.content = ''

		const attachmentID = field.$el.find( 'input[type=hidden]' ).val()
		attachments.push( attachmentID )
		if ( attachmentCache.get( attachmentID, 'attachment' ) ) {
			field.content += getAttachmentContent( attachmentID )
		}

		return field
	} )

	attachmentCache.refresh( attachments )

	return fields
}
