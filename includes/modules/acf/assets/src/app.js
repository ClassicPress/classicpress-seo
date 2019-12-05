/*global classicSEOApp*/
import $ from 'jquery'
import collect from './collect'
import { addFilter } from '@wordpress/hooks'

class App {
	analysisTimeout = 0

	constructor() {
		addFilter( 'cpseo_content', 'cpseo', collect.append.bind( collect ) )
		if ( classicSEO.acf.enableReload ) {
			this.events()
		}
	}

	events() {
		$( '.acf-field' ).on( 'change', () => {
			this.maybeRefresh()
		} )
	}

	maybeRefresh() {
		if ( this.analysisTimeout ) {
			clearTimeout( this.analysisTimeout )
		}
		
		this.analysisTimeout = setTimeout( function() {
			classicSEOApp.refresh( 'content' )
		}, classicSEO.acf.refreshRate )
	}
}

export default App
