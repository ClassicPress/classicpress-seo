import App from './src/app.js'

jQuery( document ).ready( function() {
	if ( 'undefined' !== typeof cpseoApp ) {
		window.cpseoACFAnalysis = new App()
	}
})
