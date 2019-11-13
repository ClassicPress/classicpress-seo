import jQuery from 'jquery'
import App from './src/app.js'

jQuery( document ).ready( function() {
	if ( 'undefined' !== typeof classicSEOApp ) {
		window.classicSEOACFAnalysis = new App()
	}
})
