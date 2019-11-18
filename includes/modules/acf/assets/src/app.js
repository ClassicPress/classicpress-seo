/*global classicSEOApp*/
import $ from 'jquery'
import collect from './collect'
import { addFilter } from '@wordpress/hooks'

class App {
	analysisTimeout = 0

	constructor() {
		classicSEOApp.registerPlugin( classicSEO.acf.pluginName )
		addFilter( 'cpseo_content', classicSEO.acf.pluginName, collect.append.bind( collect ) )

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
			classicSEOApp.reloadPlugin( classicSEO.acf.pluginName )
		}, classicSEO.acf.refreshRate )
	}
}

export default App
