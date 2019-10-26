var collect = require( './collect.js' )

var analysisTimeout = 0

var App = function() {
	cpseoApp.registerPlugin( classicPress.acf.pluginName )
	wp.hooks.addFilter( 'cpseo_content', classicPress.acf.pluginName, collect.append.bind( collect ) )

	if( classicPress.acf.enableReload ) {
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
		cpseoApp.reloadPlugin( classicPress.acf.pluginName )
	}, classicPress.acf.refreshRate )
}

module.exports = App
