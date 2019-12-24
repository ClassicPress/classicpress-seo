(function($){
	var ClassicSEOIntegration = function() {
			this.hooks()
		}

		ClassicSEOIntegration.prototype.hooks = function() {
			classicSEOApp.registerPlugin( 'bb-seo' )
			wp.hooks.addFilter( 'cpseo_content', 'bb-seo', function(content) {
				return window.classicSEO.beaverbuilder.pagedata;
			} )
		}

		ClassicSEOIntegration.prototype.getContent = function( content ) {
			return window.classicSEO.beaverbuilder.pagedata;
		}

		$( document ).ready( function () {
			new ClassicSEOIntegration()
	})
})(jQuery);
