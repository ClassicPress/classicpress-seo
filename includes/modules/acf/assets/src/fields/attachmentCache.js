import cache from './cache'

class attachmentCache {
	refresh( attachmentIDs ) {
		const uncached = cache.getUncached( attachmentIDs, 'attachment' )

		if ( 0 === uncached.length ) {
			return
		}

		window.wp.ajax.post( 'query-attachments', { query: { post__in: uncached } } )
			.done( function( attachments ) {
				_.each( attachments, function( attachment ) {
					cache.set( attachment.id, attachment, 'attachment' )
					window.classicSEOACFAnalysis.maybeRefresh()
				} )
			} )
	}

	get( id ) {
		const attachment = cache.get( id, 'attachment' )
		if ( ! attachment ) {
			return false
		}

		const changedAttachment = window.wp.media.attachment( id )
		if ( changedAttachment.has( 'alt' ) ) {
			attachment.alt = changedAttachment.get( 'alt' )
		}

		if ( changedAttachment.has( 'title' ) ) {
			attachment.title = changedAttachment.get( 'title' )
		}

		return attachment
	}
}

export default new attachmentCache()
