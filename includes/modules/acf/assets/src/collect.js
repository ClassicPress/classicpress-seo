import $ from 'jquery'

const fields = {
	text: require( './fields/text.js' ),
	textarea: require( './fields/textarea.js' ),
	email: require( './fields/email.js' ),
	url: require( './fields/url.js' ),
	link: require( './fields/link.js' ),
	wysiwyg: require( './fields/wysiwyg.js' ),
	image: require( './fields/image.js' ),
	gallery: require( './fields/gallery.js' ),
	taxonomy: require( './fields/taxonomy.js' ),
}

class Collect {
	getContent() {
		let fieldData = this.filterFields( this.getData() )
		const usedTypes = _.uniq( _.pluck( fieldData, 'type' ) )

		$.each( usedTypes, function( key, type ) {
			if ( type in fields ) {
				fieldData = new fields[ type ].default( fieldData )
			}
		} )

		return fieldData
	}

	append( data ) {
		const fieldData = this.getContent()
		_.each( fieldData, function( field ) {
			if ( 'undefined' !== typeof field.content && '' !== field.content ) {
				data += '\n' + field.content
			}
		} )

		return data
	}

	getData() {
		const innerFields = []
		const outerFields = []
		const outerFieldsName = [
			'flexible_content',
			'repeater',
			'group',
		]

		const acfFields = _.map( acf.get_fields(), function( field ) {
			let fieldData = $.extend( true, {}, acf.get_data( $( field ) ) )
			fieldData.$el = $( field )
			fieldData.post_meta_key = fieldData.name

			// Collect nested and parent
			if ( -1 === outerFieldsName.indexOf( fieldData.type ) ) {
				innerFields.push( fieldData )
			} else {
				outerFields.push( fieldData )
			}

			return fieldData
		} )

		if ( 0 === outerFields.length ) {
			return acfFields
		}

		_.each( innerFields, function( inner ) {
			_.each( outerFields, function( outer ) {
				if ( $.contains( outer.$el[ 0 ], inner.$el[ 0 ] ) ) {
					if ( 'flexible_content' === outer.type || 'repeater' === outer.type ) {
						outer.children = outer.children || []
						outer.children.push( inner )
						inner.parent = outer
						inner.post_meta_key = outer.name + '_' + ( outer.children.length - 1 ) + '_' + inner.name
					}

					// Types that hold single children.
					if ( 'group' === outer.type ) {
						outer.children = [ inner ]
						inner.parent = outer
						inner.post_meta_key = outer.name + '_' + inner.name
					}
				}
			} )
		} )

		return acfFields
	}

	filterFields( fieldData ) {
		return _.filter( fieldData, function( field ) {
			return ! _.contains( classicSEO.acf.blacklistFields.type, field.type ) &&
				! _.contains( classicSEO.acf.blacklistFields.name, field.name ) && ( 'key' in field )
		} )
	}
}

export default new Collect()
