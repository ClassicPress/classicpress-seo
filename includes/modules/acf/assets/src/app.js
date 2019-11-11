var collect = require( './collect.js' )

var analysisTimeout = 0

var App = function() {
	cpseoApp.registerPlugin( classicSEO.acf.pluginName )
	wp.hooks.addFilter( 'cpseo_content', classicSEO.acf.pluginName, collect.append.bind( collect ) )

	if( classicSEO.acf.enableReload ) {
		this.events()
	}
}

App.prototype.events = function() {
	var self = this
	jQuery( '.acf-field' ).on( 'change', function() {
		self.maybeRefresh()
	})
}

App.prototype.maybeRefresh = function() {
	if ( analysisTimeout ) {
		window.clearTimeout( analysisTimeout )
	}

	analysisTimeout = window.setTimeout( function() {
		cpseoApp.reloadPlugin( classicSEO.acf.pluginName )
	}, classicSEO.acf.refreshRate )
}

module.exports = App
