const isHeadline = function( field ) {
	let level = _.find( classicSEO.acf.headlines, ( value, key ) => field.key === key )

	// It has to be an integer
	if ( level ) {
		level = parseInt( level, 10 )
	}

	// Headlines only exist from h1 to h6
	if ( level < 1 || level > 6 ) {
		level = false
	}

	return level
}

const wrapInHeadline = function( field ) {
	const level = isHeadline( field )

	if ( level ) {
		field.content = '<h' + level + '>' + field.content + '</h' + level + '>'
	} else {
		field.content = '<p>' + field.content + '</p>'
	}

	return field
}

export default function( fields ) {
	fields = _.map( fields, function( field ) {
		if ( 'text' !== field.type ) {
			return field
		}

		field.content = field.$el.find( 'input[type=text][id^=acf]' ).val()
		field = wrapInHeadline( field )

		return field
	} )

	return fields
}
