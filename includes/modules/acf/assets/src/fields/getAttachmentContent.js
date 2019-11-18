import attachmentCache from './attachmentCache'

export default function( attachmentID ) {
	let content = ''
	if ( attachmentCache.get( attachmentID, 'attachment' ) ) {
		const attachment = attachmentCache.get( attachmentID, 'attachment' )
		content += '<img src="' + attachment.url + '" alt="' + attachment.alt + '" title="' + attachment.title + '">'
	}

	return content
}
