var attachmentCache = require( './cache.attachments.js' )

var getAttachmentContent = function( attachment_id ) {
	var content = '';
	if ( attachmentCache.get( attachment_id, 'attachment' ) ) {
		var attachment = attachmentCache.get( attachment_id, 'attachment' )
		content += '<img src="' + attachment.url + '" alt="' + attachment.alt + '" title="' + attachment.title + '">'
	}

	return content
}

module.exports = getAttachmentContent
