/*global classicSEOApp*/
import jQuery from 'jquery'
import debounce from 'lodash/debounce'

/**
 * Classic SEO custom fields integration class
 */
class classicSEOProductDescription {
	constructor() {
		this.excerpt = jQuery( '#excerpt' )

		if ( undefined === this.excerpt ) {
			return
		}

		this.hooks()
	}

	/**
	 * Hook into Classic SEO App eco-system
	 */
	hooks() {
		wp.hooks.addFilter( 'cpseo_content', 'cpseo', this.getContent.bind( this ) )
		this.events()
	}

	/**
	 * Gather custom fields data for analysis
	 *
	 * @param {string} content Content
	 *
	 * @return {string} New content
	 */
	getContent( content ) {
		content += ( 'undefined' !== typeof tinymce && tinymce.activeEditor && 'excerpt' === tinymce.activeEditor.id ) ? tinymce.activeEditor.getContent() : this.excerpt.val()
		return content
	}

	/**
	 * Capture events from custom fields to refresh Classic SEO analysis
	 */
	events() {
		if ( 'undefined' !== typeof tinymce && tinymce.activeEditor && 'undefined' !== typeof tinymce.editors.excerpt ) {
			tinyMCE.editors.excerpt.on( 'keyup change', debounce( () => {
				classicSEOApp.refresh( 'content' )
			}, 500 ) )
		}
	}
}

jQuery( window ).on( 'load', () => {
	new classicSEOProductDescription()
} )
