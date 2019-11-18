export default function( fields ) {
	return _.map( fields, function( field ) {
		if ( 'link' !== field.type ) {
			return field
		}

		const title = field.$el.find( 'input[type=hidden].input-title' ).val()
		const url = field.$el.find( 'input[type=hidden].input-url' ).val()
		const target = field.$el.find( 'input[type=hidden].input-target' ).val()

		field.content = '<a href="' + url + '" target="' + target + '">' + title + '</a>'

		return field
	} )
}
