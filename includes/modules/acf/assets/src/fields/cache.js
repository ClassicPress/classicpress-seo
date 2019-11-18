class Cache {
	/**
	 * Cache Holder
	 *
	 * @type {Object}
	 */
	cache = {}

	constructor() {
		this.clear( 'all' )
	}

	set( id, value, store ) {
		store = 'undefined' === typeof store ? 'default' : store

		if ( ! ( store in this.cache ) ) {
			this.cache[ store ] = {}
		}

		this.cache[ store ][ id ] = value
	}

	get( id, store ) {
		store = 'undefined' === typeof store ? 'default' : store

		if ( store in this.cache && id in this.cache[ store ] ) {
			return this.cache[ store ][ id ]
		}

		return false
	}

	getUncached( ids, store ) {
		ids = _.uniq( ids )
		store = 'undefined' === typeof store ? 'default' : store

		return ids.filter( ( id ) => {
			return false === this.get( id, store )
		} )
	}

	clear( store ) {
		store = 'undefined' === typeof store ? 'default' : store

		if ( 'all' === store ) {
			this.cache = {}
		} else {
			this.cache[ store ] = {}
		}
	}
}

export default new Cache()