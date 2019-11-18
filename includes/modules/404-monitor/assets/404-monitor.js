/*!
* Classic SEO - 404 Monitor
*
* @version 0.9.0
* @author  Classic SEO
*/
;(function( $ ) {

	'use strict';

	// Document Ready
	$(function() {

		var classicSEO404Monitor = {
			init: function() {

				this.wrap = $( '.cpseo-404-monitor-wrap' );

				this.events();
			},

			events: function() {

				this.wrap.on( 'click', '.cpseo-404-delete', function( event ) {
					event.preventDefault();

					var $this = $( this ),
						url = $this.attr( 'href' ).replace( 'admin.php', 'admin-ajax.php' ).replace( 'action=delete', 'action=cpseo_delete_log' ).replace( 'page=', 'math=' );

					$.ajax({
						url: url,
						type: 'GET',
						success: function( results ) {

							if ( results && results.success ) {
								$this.closest( 'tr' ).fadeOut( 800, function() {
									$( this ).remove();
								});
							}
						}
					});
				});

				this.wrap.on( 'click', '.cpseo-clear-logs', function( event ) {
					event.preventDefault();

					if ( ! confirm( classicSEO.logConfirmClear )) {
						return false;
					}

					$( this ).closest( 'form' ).append( '<input type="hidden" name="action" value="clear_log">' ).submit();
				});

				$( '#doaction, #doaction2' ).on( 'click', function() {
					if ( 'redirect' === $( '#bulk-action-selector-top' ).val() ) {
						$( this ).closest( 'form' ).attr( 'action', classicSEO.redirectionsUri );
					}
				});
			}
		};

		classicSEO404Monitor.init();

	});

})( jQuery );
