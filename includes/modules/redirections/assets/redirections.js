/*!
* Classic SEO - Redirections
*
* @version 0.9.0
* @author  Classic SEO
*/
'use strict'

;( function( $ ) {

	// Document Ready
	$( function() {

		var classicSEORedirections = {
			init: function() {

				this.wrap = $( '.cpseo-redirections-wrap' )

				this.addNew()
				this.showMore()
				this.columnActions()
				this.validateForm()
				this.separateRedirectionTypes()
				this.explodePastedContent()
			},

			addNew: function() {
				var self = this,
					page = $( 'html, body' )

				this.wrap.on( 'click', '.cpseo-add-new-redirection', function( event ) {
					event.preventDefault()
					var form = self.wrap.find( '.cpseo-redirections-form' )
					if ( form.is( ':visible' ) ) {
						form.hide()
						return
					}

					// Reset data.
					form.find( '#sources_repeat > .cmb-repeatable-grouping:not(:eq(0))' ).remove()
					form.find( '> form' ).get( 0 ).reset()
					form.show()

					page.on( 'scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove', function() {
						page.stop()
					})

					page.animate({ scrollTop: form.position().top }, 'slow', function() {
						page.off( 'scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove' )
					})
				})

				this.wrap.on( 'click', '.button-link-delete', function( event ) {
					event.preventDefault()

					var $this = $( this )
					$this.closest( '.cpseo-redirections-form' ).hide()
				})
			},

			validateForm: function() {

				var buttonPrimary = $( '.cpseo-redirections-form .button-primary' )
				$( '.cpseo-redirections-form > .cmb-form' ).on( 'submit', function( event ) {
					var form       = $( this ),
						errorElems = form.find( '.validation-message' ),
						hasError   = false

					buttonPrimary.prop( 'disabled', true )

					// Clear error.
					form.find( '.invalid' ).removeClass( 'invalid' )
					errorElems.each( function() {
						$( this ).prev( 'br' ).remove()
						$( this ).remove()
					})

					// Handle error.
					form.find( 'input[type="text"]:not(.exclude)' ).each( function() {
						var input = $( this )
						if ( ! input.val() || ! input.val().trim() ) {
							hasError = true
							input.addClass( 'invalid' ).after( $( '<br><span class="validation-message">' + classicSEO.emptyError + '</span>' ) )
						}
					})

					if ( hasError ) {
						event.preventDefault()
						buttonPrimary.prop( 'disabled', false )
					}
				})
			},

			separateRedirectionTypes: function() {
				var row = this.wrap.find( '.cmb2-id-header-code' )

				if ( ! row.length ) {
					return
				}

				var clonedRow = row.clone()
				clonedRow.find( '.cmb-th label' ).text( classicSEO.maintenanceMode )
				clonedRow.find( '.cmb2-radio-list li:lt(3)' ).remove()
				clonedRow.find( '.cmb2-radio-list li' ).css( 'min-width', '28%' )
				clonedRow.find( '.cmb2-radio-list li:eq(0) label' ).width( '100%' ).css( 'textAlign', 'center' )

				row.after( clonedRow )
				row.addClass( 'nob nopb' )
				row.find( '.cmb2-radio-list li:gt(2)' ).remove()

				var group = $( '.cmb2-id-url-to' ),
					field = $( '#url_to' )

				$( '[name=header_code]' ).on( 'change', function() {
					var value = parseInt( $( this ).val() )
					if ( 410 === value || 451 === value ) {
						field.addClass( 'exclude' )
						group.addClass( 'hidden' )
					} else {
						field.removeClass( 'exclude' )
						group.removeClass( 'hidden' )
					}
				})
				$( '[name=header_code]:checked' ).trigger( 'change' )
			},

			explodePastedContent: function() {
				var group = $( '#sources_repeat' )

				group.on( 'paste', 'input', function( event ) {
					var pastedData = event.originalEvent.clipboardData.getData( 'text' )

					// Process only if it contains line break.
					var match = /\r|\n/.exec( pastedData )
					if ( ! match ) {
						return true
					}

					// Split by line break & remove empty elements.
					var input = $( this ),
						addButton = $( '.cmb-add-group-row', group ),
						items  = pastedData.split( /\r?\n/ ).filter( String ),
						comparisonValue = input.closest( '.cmb-field-list' ).find( 'select' ).val()

					// Now add them as new items
					$.each( items, function( index, item ) {
						input.val( item )
						input.closest( '.cmb-field-list' ).find( 'select' ).val( comparisonValue )
						if ( index < items.length - 1 ) {

							// Number of items to process.
							if ( ( classicSEO.redirectionPastedContent - 1 ) <= index ) {
								return false
							}

							addButton.click()
							input = $( '.cmb-repeatable-grouping', group ).last().find( 'input' )

						} else {
							input.focus()
						}
					})
					return false
				})
			},

			showMore: function() {
				this.wrap.on( 'click', '.cpseo-showmore', function( event ) {
					event.preventDefault()

					var $this = $( this )
					$this.hide()
					$this.next( '.cpseo-more' ).slideDown()
				})

				this.wrap.on( 'click', '.cpseo-hidemore', function( event ) {
					event.preventDefault()

					var $this = $( this ).parent()
					$this.hide()
					$this.prev( '.cpseo-showmore' ).show()
				})
			},

			columnActions: function() {
				var self = this

				this.wrap.on( 'click', '.cpseo-redirection-action', function( event ) {
					event.preventDefault()

					var button = $( this ),
						action = button.data( 'action' ),
						url    = this.href.replace( 'admin.php', 'admin-ajax.php' )
							.replace( 'page=cpseo-redirections&', '' ) + '&action=cpseo_redirection_' + action

					$.ajax({
						url: url,
						type: 'GET',
						success: function( results ) {
							if ( results && results.success ) {
								if ([ 'delete', 'trash', 'restore' ].includes( action ) ) {
									button.closest( 'tr' ).fadeOut( 800, function() {
										$( this ).remove()
									})
								} else {
									button.closest( 'tr' ).toggleClass( 'cpseo-redirection-activated cpseo-redirection-deactivated' )
								}

								if ( 'activate' === action ) {
									self.filterCountAdd( 'active' )
									self.filterCountSubstract( 'inactive' )
								} else if ( 'deactivate' == action ) {
									self.filterCountAdd( 'inactive' )
									self.filterCountSubstract( 'active' )
								} else if ( 'trash' == action ) {
									self.filterCountAdd( 'trashed' )
									self.filterCountSubstract( 'all' )
									if ( button.closest( '.cpseo-redirection-deactivated' ).length ) {
										self.filterCountSubstract( 'inactive' )
									} else {
										self.filterCountSubstract( 'active' )
									}
								} else if ( 'delete' == action ) {
									self.filterCountSubstract( 'trashed' )
								} else if ( 'restore' == action ) {
									self.filterCountAdd( 'active' )
									self.filterCountAdd( 'all' )
									self.filterCountSubstract( 'trashed' )
								}
							}
						}
					})
				})
			},

			filterCountAdd: function( filter ) {
				this.filterCount( filter, 'add' )
			},

			filterCountSubstract: function( filter ) {
				this.filterCount( filter, 'sub' )
			},

			filterCount: function( filter, action ) {
				var $elem = this.wrap.find( 'form > ul.subsubsub > .' + filter + ' .count' ),
					count = parseInt( $elem.text().substr( 1 ) )

				count = 'add' === action ? count + 1 : count - 1
				$elem.text( '(' + count + ')' )
			}
		}

		classicSEORedirections.init()

	})

}( jQuery ) )
