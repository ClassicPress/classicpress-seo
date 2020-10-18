<?php
/**
 * The Local SEO Module
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Local_Seo

 */

namespace Classic_SEO\Local_Seo;

use Classic_SEO\Post;
use Classic_SEO\Helper;
use Classic_SEO\Traits\Ajax;
use Classic_SEO\Traits\Hooker;
use Classic_SEO\Helpers\Str;
use Classic_SEO\Helpers\Param;

defined( 'ABSPATH' ) || exit;

/**
 * Local_Seo class.
 */
class Local_Seo {

	use Ajax, Hooker;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->ajax( 'search_pages', 'search_pages' );
		$this->filter( 'cpseo/settings/title', 'add_settings' );
		$this->filter( 'cpseo/json_ld', 'organization_or_person', 15, 2 );
	}

	/**
	 * Add module settings into general optional panel.
	 *
	 * @param array $tabs Array of option panel tabs.
	 *
	 * @return array
	 */
	public function add_settings( $tabs ) {
		$tabs['local']['file'] = dirname( __FILE__ ) . '/views/titles-options.php';

		return $tabs;
	}

	/**
	 * Ajax search pages.
	 */
	public function search_pages() {
		$term = Param::get( 'term' );
		if ( empty( $term ) ) {
			exit;
		}

		$pages = get_posts([
			's'              => $term,
			'post_type'      => 'page',
			'posts_per_page' => -1,
		]);

		$data = [];
		foreach ( $pages as $page ) {
			$data[] = [
				'id'   => $page->ID,
				'text' => $page->post_title,
			];
		}

		wp_send_json( [ 'results' => $data ] );
	}

	/**
	 * Output structured data for Person or Organization.
	 *
	 * @param array  $data    Array of JSON-LD data.
	 * @param JsonLD $json_ld The JsonLD instance.
	 *
	 * @return array
	 */
	public function organization_or_person( $data, $json_ld ) {
		if ( ! $this->can_output_schema() ) {
			return $data;
		}

		$entity = [
			'@context' => 'https://schema.org',
			'@type'    => '',
			'@id'      => '',
			'name'     => '',
			'url'      => get_home_url(),
			'sameAs'   => $this->get_social_profiles(),
		];

		$json_ld->add_prop( 'email', $entity );
		$json_ld->add_prop( 'url', $entity );
		$json_ld->add_prop( 'address', $entity );
		$json_ld->add_prop( 'image', $entity );

		switch ( Helper::get_settings( 'titles.cpseo_knowledgegraph_type' ) ) {
			case 'company':
				$data['Organization'] = $this->organization( $entity );
				break;
			case 'person':
				$data['Person'] = $this->person( $entity, $json_ld );
				break;
		}

		return $data;
	}

	/**
	 * Check if schema data can be output.
	 *
	 * @return bool
	 */
	private function can_output_schema() {
		$post_id = Post::get_simple_page_id();
		$pages   = array_map( 'absint', array_filter( [ Helper::get_settings( 'titles.cpseo_local_seo_about_page' ), Helper::get_settings( 'titles.cpseo_local_seo_contact_page' ) ] ) );
		if ( $post_id > 0 && ! in_array( $post_id, $pages, true ) && ! is_front_page() ) {
			return false;
		}

		return true;
	}

	/**
	 * Structured data for Organization.
	 *
	 * @param array $entity Array of JSON-LD entity.
	 */
	private function organization( $entity ) {
		$name            = Helper::get_settings( 'titles.cpseo_knowledgegraph_name' );
		$type            = Helper::get_settings( 'titles.cpseo_local_business_type' );
		$entity['@type'] = $type ? $type : 'Organization';
		$entity['@id']   = get_home_url() . '#organization';
		$entity['name']  = $name ? $name : get_bloginfo( 'name' );

		$this->add_contact_points( $entity );
		$this->add_business_hours( $entity );

		// Price Range.
		if ( $price_range = Helper::get_settings( 'titles.cpseo_price_range' ) ) { // phpcs:ignore
			$entity['priceRange'] = $price_range;
		}

		return $this->sanitize_organization_schema( $entity, $entity['@type'] );
	}

	/**
	 * Structured data for Person.
	 *
	 * @param array  $entity  Array of JSON-LD entity.
	 * @param JsonLD $json_ld JsonLD instance.
	 */
	private function person( $entity, $json_ld ) {
		$name = Helper::get_settings( 'titles.cpseo_knowledgegraph_name' );
		if ( ! $name ) {
			return false;
		}

		$entity['@type'] = 'Person';
		$entity['@id']   = '#person';
		$entity['name']  = $name;
		$json_ld->add_prop( 'phone', $entity );

		if ( isset( $entity['logo'] ) ) {
			$entity['image'] = $entity['logo'];
			unset( $entity['logo'] );
		}

		return $entity;
	}

	/**
	 * Add Contact Points.
	 *
	 * @param array $entity Array of JSON-LD entity.
	 */
	private function add_contact_points( &$entity ) {
		$phone_numbers = Helper::get_settings( 'titles.cpseo_phone_numbers' );
		if ( ! isset( $phone_numbers[0]['number'] ) ) {
			return;
		}

		$entity['contactPoint'] = [];
		foreach ( $phone_numbers as $number ) {
			$entity['contactPoint'][] = [
				'@type'       => 'ContactPoint',
				'telephone'   => $number['number'],
				'contactType' => $number['type'],
			];
		}
	}

	/**
	 * Add business hours.
	 *
	 * @param array $entity Array of JSON-LD entity.
	 */
	private function add_business_hours( &$entity ) {
		$opening_hours = $this->get_opening_hours();
		if ( empty( $opening_hours ) ) {
			return;
		}

		$entity['openingHours'] = [];
		foreach ( $opening_hours as $time => $days ) {
			$entity['openingHours'][] = join( ',', $days ) . ' ' . $time;
		}
	}

	/**
	 * Get opening hours.
	 *
	 * @return bool|array
	 */
	private function get_opening_hours() {
		$hours = Helper::get_settings( 'titles.cpseo_opening_hours' );
		if ( ! is_array( $hours ) ) {
			return false;
		}

		$opening_hours = [];
		foreach ( $hours as $hour ) {
			if ( empty( $hour['time'] ) ) {
				continue;
			}

			$opening_hours[ $hour['time'] ][] = $hour['day'];
		}

		return $opening_hours;
	}

	/**
	 * Sanitize structured data for different organization types.
	 *
	 * @param array  $entity Array of Schema structured data.
	 * @param string $type   Type of organization.
	 *
	 * @return array Sanitized data.
	 */
	private function sanitize_organization_schema( $entity, $type ) {
		$types = [
			'ecp'  => [ 'Zoo', 'Airport', 'Beach', 'BusStation', 'BusStop', 'Cemetery', 'Crematorium', 'TaxiStand', 'TrainStation', 'EventVenue', 'Museum', 'MusicVenue', 'PlaceOfWorship', 'Buddhist Temple', 'CatholicChurch', 'Church', 'Hindu Temple', 'Mosque', 'Synagogue', 'RVPark', 'SubwayStation', 'GovernmentBuilding', 'CityHall', 'Courthouse', 'DefenceEstablishment', 'Embassy', 'LegislativeBuilding', 'ParkingFacility', 'Park', 'PerformingArtsTheater', 'Playground' ],
			'op'   => [ 'Organization', 'Corporation', 'EducationalOrganization', 'CollegeorUniversity', 'ElementarySchool', 'HighSchool', 'MiddleSchool', 'Preschool', 'School', 'SportsTeam', 'MedicalOrganization', 'Dentist', 'DiagnosticLab', 'Pharmacy', 'VeterinaryCare', 'PerformingGroup', 'DanceGroup', 'MusicGroup', 'TheaterGroup', 'GovernmentOrganization', 'NGO' ],
			'opec' => [ 'Residence', 'ApartmentComplex', 'GatedResidenceCommunity', 'SingleFamilyResidence', 'Aquarium' ],
			'logo' => [ 'AnimalShelter', 'AutomotiveBusiness', 'Campground', 'ChildCare', 'DryCleaningorLaundry', 'EmergencyService', 'FireStation', 'PoliceStation', 'EntertainmentBusiness', 'AdultEntertainment', 'AmusementPark', 'ArtGallery', 'Casino', 'ComedyClub', 'NightClub', 'EmploymentAgency', 'TravelAgency', 'Store', 'BikeStore', 'BookStore', 'ClothingStore', 'ComputerStore', 'ConvenienceStore', 'DepartmentStore', 'ElectronicsStore', 'Florist', 'FurnitureStore', 'GardenStore', 'GroceryStore', 'HardwareStore', 'HobbyShop', 'HomeGoodsStore', 'JewelryStore', 'LiquorStore', 'MensClothingStore', 'MobilePhoneStore', 'MovieRentalStore', 'MusicStore', 'OfficeEquipmentStore', 'OutletStore', 'PawnShop', 'PetStore', 'ShoeStore', 'SportingGoodsStore', 'TireShop', 'ToyStore', 'WholesaleStore', 'FinancialService', 'Hospital', 'MovieTheater', 'HomeAndConstructionBusiness', 'Electrician', 'GeneralContractor', 'Plumber', 'InternetCafe', 'Library', 'LocalBusiness', 'LodgingBusiness', 'Hostel', 'Hotel', 'Motel', 'BedAndBreakfast', 'RadioStation', 'RealEstateAgent', 'RecyclingCenter', 'SelfStorage', 'ShoppingCenter', 'SportsActivityLocation', 'BowlingAlley', 'ExerciseGym', 'GolfCourse', 'HealthClub', 'PublicSwimmingPool', 'SkiResort', 'SportsClub', 'TennisComplex', 'StadiumOrArena', 'TelevisionStation', 'TouristInformationCenter', 'MovingCompany', 'InsuranceAgency', 'ProfessionalService', 'HVACBusiness', 'AutoBodyShop', 'AutoDealer', 'AutoPartsStore', 'AutoRental', 'AutoRepair', 'AutoWash', 'GasStation', 'MotorcycleDealer', 'MotorcycleRepair', 'AccountingService', 'AutomatedTeller', 'BankOrCreditUnion', 'FoodEstablishment', 'Bakery', 'BarOrPub', 'Brewery', 'CafeorCoffeeShop', 'FastFoodRestaurant', 'IceCreamShop', 'Restaurant', 'Winery', 'GovernmentOffice', 'PostOffice', 'HealthAndBeautyBusiness', 'BeautySalon', 'DaySpa', 'HairSalon', 'HealthClub', 'NailSalon', 'TattooParlor', 'HousePainter', 'Locksmith', 'Notary', 'RoofingContractor', 'LegalService', 'Physician', 'Optician', 'MedicalClinic' ],
		];

		$perform = false;
		foreach ( $types as $func => $to_check ) {
			if ( in_array( $type, $to_check, true ) ) {
				$perform = 'sanitize_organization_' . $func;
				break;
			}
		}

		return $perform ? $this->$perform( $entity ) : $entity;
	}

	/**
	 * Remove `email`, `contactPoint`, `priceRange` properties
	 * from the Schema entity.
	 *
	 * @param array $entity Array of Schema structured data.
	 *
	 * @return array Sanitized data.
	 */
	private function sanitize_organization_ecp( $entity ) {
		unset( $entity['email'], $entity['contactPoint'], $entity['priceRange'] );

		return $entity;
	}

	/**
	 * Remove `openingHours`, `priceRange` properties
	 * from the Schema entity.
	 *
	 * @param array $entity Array of Schema structured data.
	 *
	 * @return array Sanitized data.
	 */
	private function sanitize_organization_op( $entity ) {
		unset( $entity['openingHours'], $entity['priceRange'] );

		return $entity;
	}

	/**
	 * Remove `openingHours`, `priceRange`, `email`, `contactPoint` properties
	 * from the Schema entity.
	 *
	 * @param array $entity Array of Schema structured data.
	 *
	 * @return array Sanitized data.
	 */
	private function sanitize_organization_opec( $entity ) {
		unset( $entity['openingHours'], $entity['priceRange'], $entity['email'], $entity['contactPoint'] );

		return $entity;
	}

	/**
	 * Change `logo` property to `image` & `contactPoint` to `telephone`.
	 *
	 * @param array $entity Array of schema data.
	 *
	 * @return array Sanitized data.
	 */
	private function sanitize_organization_logo( $entity ) {
		if ( isset( $entity['logo'] ) ) {
			$entity['image'] = $entity['logo'];
			unset( $entity['logo'] );
		}
		if ( isset( $entity['contactPoint'] ) ) {
			$entity['telephone'] = $entity['contactPoint'][0]['telephone'];
			unset( $entity['contactPoint'] );
		}

		return $entity;
	}

	/**
	 * Get global social profile URLs, to use in the `sameAs` property.
	 *
	 * @link https://developers.google.com/webmasters/structured-data/customize/social-profiles
	 */
	private function get_social_profiles() {
		$services = [
			'facebook',
			'twitter',
			'google_places',
			'linkedin',
			'instagram',
			'youtube',
			'pinterest',
		];

		$profiles = [];
		foreach ( $services as $profile ) {
			if ( $profile = Helper::get_settings( 'titles.cpseo_social_url_' . $profile ) ) { // phpcs:ignore
				$profiles[] = $profile;
			}
		}

		return $profiles;
	}
}
