/*!
* ClassicPress SEO - Status & Tools
*
* @version 1.0.33
* @author  ClassicPress SEO
*/
;( function( $ ) {

	'use strict'

	$( function() {

		var after = $( '.nav-tab-wrapper' )

		function addNotice( msg, which, fadeout ) {
			which   = which || 'error'
			fadeout = fadeout || 3000
			var notice = $( '<div class="notice notice-' + which + ' is-dismissible"><p>' + msg + '</p></div>' ).hide()
			after.next( '.notice' ).remove()
			after.after( notice )
			notice.slideDown()
			$( document ).trigger( 'wp-updates-notice-added' )
			if ( fadeout ) {
				setTimeout( function() {
					notice.fadeOut()
				}, fadeout )
			}
		}

		$( '.tools-action' ).on( 'click', function( event ) {
			event.preventDefault()

			var $this = $( this )
			if ( $this.data( 'confirm' ) && ! confirm( $this.data( 'confirm' ) ) ) {
				return false
			}

			$this.attr( 'disabled', 'disabled' )
			$.ajax({
				url: classicPress.api.root + 'cpseo/v1/toolsAction',
				method: 'POST',
				beforeSend: function( xhr ) {
					xhr.setRequestHeader( 'X-WP-Nonce', classicPress.api.nonce )
				},
				data: {
					action: $this.data( 'action' )
				}
			}).always( function() {
				$this.removeAttr( 'disabled' )
			}).fail( function( response ) {
				addNotice( response.statusText )
			}).done( function( response ) {
				if ( response ) {
					addNotice( response, 'success' )
					return
				}

				addNotice( 'Something went wrong. Please try again later.' )
			})

			return false
		})
	})

}( jQuery ) )
