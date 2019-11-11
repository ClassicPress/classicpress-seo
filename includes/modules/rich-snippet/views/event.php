<?php
/**
 * Metabox - Event Rich Snippet
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet
 */

$event = [ [ 'cpseo_rich_snippet', 'event' ] ];

$cmb->add_field([
	'id'      => 'cpseo_snippet_event_type',
	'type'    => 'select',
	'name'    => esc_html__( 'Event Type', 'cpseo' ),
	'desc'    => esc_html__( 'Type of the event.', 'cpseo' ),
	'options' => [
		'Event'            => esc_html__( 'Event', 'cpseo' ),
		'BusinessEvent'    => esc_html__( 'Business Event', 'cpseo' ),
		'ChildrensEvent'   => esc_html__( 'Childrens Event', 'cpseo' ),
		'ComedyEvent'      => esc_html__( 'Comedy Event', 'cpseo' ),
		'DanceEvent'       => esc_html__( 'Dance Event', 'cpseo' ),
		'DeliveryEvent'    => esc_html__( 'Delivery Event', 'cpseo' ),
		'EducationEvent'   => esc_html__( 'Education Event', 'cpseo' ),
		'ExhibitionEvent'  => esc_html__( 'Exhibition Event', 'cpseo' ),
		'Festival'         => esc_html__( 'Festival', 'cpseo' ),
		'FoodEvent'        => esc_html__( 'Food Event', 'cpseo' ),
		'LiteraryEvent'    => esc_html__( 'Literary Event', 'cpseo' ),
		'MusicEvent'       => esc_html__( 'Music Event', 'cpseo' ),
		'PublicationEvent' => esc_html__( 'Publication Event', 'cpseo' ),
		'SaleEvent'        => esc_html__( 'Sale Event', 'cpseo' ),
		'ScreeningEvent'   => esc_html__( 'Screening Event', 'cpseo' ),
		'SocialEvent'      => esc_html__( 'Social Event', 'cpseo' ),
		'SportsEvent'      => esc_html__( 'Sports Event', 'cpseo' ),
		'TheaterEvent'     => esc_html__( 'Theater Event', 'cpseo' ),
		'VisualArtsEvent'  => esc_html__( 'Visual Arts Event', 'cpseo' ),
	],
	'default' => 'Event',
	'classes' => 'cmb-row-33',
	'dep'     => $event,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_event_venue',
	'type'    => 'text',
	'name'    => esc_html__( 'Venue Name', 'cpseo' ),
	'desc'    => esc_html__( 'The venue name.', 'cpseo' ),
	'classes' => 'cmb-row-50',
	'dep'     => $event,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_event_venue_url',
	'type'       => 'text_url',
	'name'       => esc_html__( 'Venue URL', 'cpseo' ),
	'desc'       => esc_html__( 'Website URL of the venue', 'cpseo' ),
	'classes'    => 'cpseo-validate-field',
	'attributes' => [ 'data-rule-url' => 'true' ],
	'dep'        => $event,
]);

$cmb->add_field([
	'id'   => 'cpseo_snippet_event_address',
	'type' => 'address',
	'name' => esc_html__( 'Address', 'cpseo' ),
	'dep'  => $event,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_event_performer_type',
	'type'    => 'radio_inline',
	'name'    => esc_html__( 'Performer', 'cpseo' ),
	'options' => [
		'Person'       => esc_html__( 'Person', 'cpseo' ),
		'Organization' => esc_html__( 'Organization', 'cpseo' ),
	],
	'classes' => 'cmb-row-33 nob',
	'default' => 'Person',
	'dep'     => $event,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_event_performer',
	'type'    => 'text',
	'name'    => esc_html__( 'Performer Name', 'cpseo' ),
	'desc'    => esc_html__( 'A performer at the event', 'cpseo' ),
	'classes' => 'cmb-row-50',
	'dep'     => $event,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_event_performer_url',
	'type'       => 'text',
	'name'       => esc_html__( 'Performer URL', 'cpseo' ),
	'attributes' => [
		'data-rule-url' => 'true',
	],
	'classes'    => 'cpseo-validate-field',
	'dep'        => $event,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_event_status',
	'type'    => 'select',
	'name'    => esc_html__( 'Event Status', 'cpseo' ),
	'desc'    => esc_html__( 'Current status of the event (optional)', 'cpseo' ),
	'options' => [
		''                 => esc_html__( 'None', 'cpseo' ),
		'EventScheduled'   => esc_html__( 'Scheduled', 'cpseo' ),
		'EventCancelled'   => esc_html__( 'Cancelled', 'cpseo' ),
		'EventPostponed'   => esc_html__( 'Postponed', 'cpseo' ),
		'EventRescheduled' => esc_html__( 'Rescheduled', 'cpseo' ),
	],
	'classes' => 'cmb-row-33',
	'dep'     => $event,
]);

$cmb->add_field([
	'id'          => 'cpseo_snippet_event_startdate',
	'type'        => 'text_datetime_timestamp',
	'date_format' => 'Y-m-d',
	'name'        => esc_html__( 'Start Date', 'cpseo' ),
	'desc'        => esc_html__( 'Date and time of the event.', 'cpseo' ),
	'classes'     => 'cmb-row-33',
	'dep'         => $event,
]);

$cmb->add_field([
	'id'          => 'cpseo_snippet_event_enddate',
	'type'        => 'text_datetime_timestamp',
	'date_format' => 'Y-m-d',
	'name'        => esc_html__( 'End Date', 'cpseo' ),
	'desc'        => esc_html__( 'End date and time of the event.', 'cpseo' ),
	'classes'     => 'cmb-row-33',
	'dep'         => $event,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_event_ticketurl',
	'type'       => 'text',
	'name'       => esc_html__( 'Ticket URL', 'cpseo' ),
	'desc'       => esc_html__( 'A URL where visitors can purchase tickets for the event.', 'cpseo' ),
	'classes'    => 'cmb-row-33 cpseo-validate-field',
	'attributes' => [
		'data-rule-url' => 'true',
	],
	'dep'        => $event,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_event_price',
	'type'       => 'text',
	'name'       => esc_html__( 'Entry Price', 'cpseo' ),
	'desc'       => esc_html__( 'Entry price of the event (optional)', 'cpseo' ),
	'classes'    => 'cmb-row-33',
	'dep'        => $event,
	'attributes' => [
		'type' => 'number',
		'step' => 'any',
	],
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_event_currency',
	'type'       => 'text',
	'name'       => esc_html__( 'Currency', 'cpseo' ),
	'desc'       => esc_html__( 'ISO 4217 Currency code. Example: EUR', 'cpseo' ),
	'classes'    => 'cmb-row-33 cpseo-validate-field',
	'attributes' => [
		'data-rule-regex'       => 'true',
		'data-validate-pattern' => '^[A-Z]{3}$',
		'data-msg-regex'        => esc_html__( 'Please use the correct format. Example: EUR', 'cpseo' ),
	],
	'dep'        => $event,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_event_availability',
	'type'    => 'select',
	'name'    => esc_html__( 'Availability', 'cpseo' ),
	'desc'    => esc_html__( 'Offer availability', 'cpseo' ),
	'options' => [
		''         => esc_html__( 'None', 'cpseo' ),
		'InStock'  => esc_html__( 'In Stock', 'cpseo' ),
		'SoldOut'  => esc_html__( 'Sold Out', 'cpseo' ),
		'PreOrder' => esc_html__( 'Preorder', 'cpseo' ),
	],
	'classes' => 'cmb-row-33 nob',
	'dep'     => $event,
]);

$cmb->add_field([
	'id'          => 'cpseo_snippet_event_availability_starts',
	'type'        => 'text_datetime_timestamp',
	'date_format' => 'Y-m-d',
	'name'        => esc_html__( 'Availability Starts', 'cpseo' ),
	'desc'        => esc_html__( 'Date and time when offer is made available. (optional)', 'cpseo' ),
	'classes'     => 'cmb-row-33 nob',
	'dep'         => $event,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_event_inventory',
	'type'       => 'text',
	'name'       => esc_html__( 'Stock Inventory', 'cpseo' ),
	'desc'       => esc_html__( 'Number of tickets (optional)', 'cpseo' ),
	'classes'    => 'cmb-row-33 nob',
	'dep'        => $event,
	'attributes' => [ 'type' => 'number' ],
]);
