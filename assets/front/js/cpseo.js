/*!
* ClassicPress SEO
*
* @version 0.0.5
*/
;(function( $ ) {

	'use strict';

	// Document Ready
	$(function() {

		window.classicPressFront = {

			init: function() {
				this.adminMenu();
			},

			adminMenu: function() {
				var menu = $( '#wp-admin-bar-cpseo-mark-me' ),
					self = this,
					icon = '<span class="dashicons dashicons-yes" style="font-family: dashicons; font-size: 19px;"></span>';

				menu.on( 'click', '.mark-page-as a', function( event ) {
					event.preventDefault();
					self.ajax( 'mark_page_as', {
						objectID: classicPress.objectID,
						objectType: classicPress.objectType,
						what: $( this ).attr( 'href' ).replace( '#', '' )
					} );

					if ( $(this).find('.dashicons').length ) {
						$(this).find('.dashicons').remove();
					} else {
						$(this).prepend(icon);
					}
				});
			},

			ajax: function( action, data, method ) {
				return $.ajax({
					url: classicPress.ajaxurl,
					type: method || 'POST',
					dataType: 'json',
					data: $.extend( true, {
						action: 'cpseo_' + action,
						security: classicPress.security
					}, data )
				});
			}
		};

		window.classicPressFront.init();
	});

})( jQuery );
