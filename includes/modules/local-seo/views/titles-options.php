<?php
/**
 * The local seo settings.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Local_Seo
 */

use Classic_SEO\Helper;

$company = [ [ 'cpseo_knowledgegraph_type', 'company' ] ];
$person  = [ [ 'cpseo_knowledgegraph_type', 'person' ] ];

$cmb->add_field([
	'id'      => 'cpseo_knowledgegraph_type',
	'type'    => 'radio_inline',
	'name'    => esc_html__( 'Person or Company', 'cpseo' ),
	'options' => [
		'person'  => esc_html__( 'Person', 'cpseo' ),
		'company' => esc_html__( 'Organization', 'cpseo' ),
	],
	'desc'    => esc_html__( 'Choose whether the site represents a person or an organization.', 'cpseo' ),
	'default' => 'person',
]);

$cmb->add_field([
	'id'      => 'cpseo_knowledgegraph_name',
	'type'    => 'text',
	'name'    => esc_html__( 'Name', 'cpseo' ),
	'desc'    => esc_html__( 'Your name or company name', 'cpseo' ),
	'default' => get_bloginfo( 'name' ),
]);

$cmb->add_field([
	'id'      => 'cpseo_knowledgegraph_logo',
	'type'    => 'file',
	'name'    => esc_html__( 'Logo', 'cpseo' ),
	'desc'    => __( '<strong>Min Size: 160Χ90px, Max Size: 1920X1080px</strong>.<br /> A squared image is preferred by the search engines.', 'cpseo' ),
	'options' => [ 'url' => false ],
]);

$cmb->add_field([
	'id'      => 'cpseo_url',
	'type'    => 'text',
	'name'    => esc_html__( 'URL', 'cpseo' ),
	'desc'    => esc_html__( 'URL of the item.', 'cpseo' ),
	'default' => site_url(),
]);

$cmb->add_field([
	'id'   => 'cpseo_email',
	'type' => 'text',
	'name' => esc_html__( 'Email', 'cpseo' ),
	'desc' => esc_html__( 'Search engines display your email address.', 'cpseo' ),
]);

$cmb->add_field([
	'id'   => 'cpseo_phone',
	'type' => 'text',
	'name' => esc_html__( 'Phone', 'cpseo' ),
	'desc' => esc_html__( 'Search engines may prominently display your contact phone number for mobile users.', 'cpseo' ),
	'dep'  => $person,
]);

$cmb->add_field([
	'id'   => 'cpseo_local_address',
	'type' => 'address',
	'name' => esc_html__( 'Address', 'cpseo' ),
]);

$cmb->add_field([
	'id'         => 'cpseo_local_address_format',
	'type'       => 'textarea_small',
	'name'       => esc_html__( 'Address Format', 'cpseo' ),
	'desc'       => wp_kses_post( __( 'Format used when the address is displayed using the <code>[cpseo_contact_info]</code> shortcode.<br><strong>Available Tags: {address}, {locality}, {region}, {postalcode}, {country}</strong>', 'cpseo' ) ),
	'default'    => '{address} {locality}, {region} {postalcode}, {country}',
	'classes'    => 'cpseo-address-format',
	'attributes' => [
		'rows'        => 2,
		'placeholder' => '{address} {locality}, {region} {country}. {postalcode}.',
	],
	'dep'        => $company,
]);

$cmb->add_field([
	'id'         => 'cpseo_local_business_type',
	'type'       => 'select',
	'name'       => esc_html__( 'Business Type', 'cpseo' ),
	'options'    => Helper::choices_business_types( true ),
	'attributes' => ( 'data-s2' ),
	'dep'        => $company,
]);

$opening_hours = $cmb->add_field([
	'id'      => 'cpseo_opening_hours',
	'type'    => 'group',
	'name'    => esc_html__( 'Opening Hours', 'cpseo' ),
	'desc'    => esc_html__( 'Select opening hours. You can add multiple sets if you have different opening or closing hours on some days or if you have a mid-day break. Times are specified using 24:00 time.', 'cpseo' ),
	'options' => [
		'add_button'    => esc_html__( 'Add time', 'cpseo' ),
		'remove_button' => esc_html__( 'Remove', 'cpseo' ),
	],
	'dep'     => $company,
	'classes' => 'cmb-group-text-only',
]);

$cmb->add_group_field( $opening_hours, [
	'id'      => 'day',
	'type'    => 'select',
	'options' => [
		'Monday'    => esc_html__( 'Monday', 'cpseo' ),
		'Tuesday'   => esc_html__( 'Tuesday', 'cpseo' ),
		'Wednesday' => esc_html__( 'Wednesday', 'cpseo' ),
		'Thursday'  => esc_html__( 'Thursday', 'cpseo' ),
		'Friday'    => esc_html__( 'Friday', 'cpseo' ),
		'Saturday'  => esc_html__( 'Saturday', 'cpseo' ),
		'Sunday'    => esc_html__( 'Sunday', 'cpseo' ),
	],
]);

$cmb->add_group_field( $opening_hours, [
	'id'         => 'time',
	'type'       => 'text',
	'default'    => '09:00-17:00',
	'attributes' => [ 'placeholder' => esc_html__( 'e.g. 09:00-17:00', 'cpseo' ) ],
]);

$cmb->add_field([
	'id'      => 'cpseo_opening_hours_format',
	'type'    => 'switch',
	'name'    => esc_html__( 'Opening Hours Format', 'cpseo' ),
	'options' => [
		'off' => '24:00',
		'on'  => '12:00',
	],
	'desc'    => esc_html__( 'Time format used in the contact shortcode.', 'cpseo' ),
	'default' => 'off',
	'dep'     => $company,
]);

$phones = $cmb->add_field([
	'id'      => 'cpseo_phone_numbers',
	'type'    => 'group',
	'name'    => esc_html__( 'Phone Number', 'cpseo' ),
	'desc'    => esc_html__( 'Search engines may prominently display your contact phone number for mobile users.', 'cpseo' ),
	'options' => [
		'add_button'    => esc_html__( 'Add number', 'cpseo' ),
		'remove_button' => esc_html__( 'Remove', 'cpseo' ),
	],
	'dep'     => $company,
	'classes' => 'cmb-group-text-only',
]);

$cmb->add_group_field( $phones, [
	'id'      => 'type',
	'type'    => 'select',
	'options' => [
		'customer support'    => esc_html__( 'Customer Service', 'cpseo' ),
		'technical support'   => esc_html__( 'Technical Support', 'cpseo' ),
		'billing support'     => esc_html__( 'Billing Support', 'cpseo' ),
		'bill payment'        => esc_html__( 'Bill Payment', 'cpseo' ),
		'sales'               => esc_html__( 'Sales', 'cpseo' ),
		'reservations'        => esc_html__( 'Reservations', 'cpseo' ),
		'credit card support' => esc_html__( 'Credit Card Support', 'cpseo' ),
		'emergency'           => esc_html__( 'Emergency', 'cpseo' ),
		'baggage tracking'    => esc_html__( 'Baggage Tracking', 'cpseo' ),
		'roadside assistance' => esc_html__( 'Roadside Assistance', 'cpseo' ),
		'package tracking'    => esc_html__( 'Package Tracking', 'cpseo' ),
	],
	'default' => 'customer_support',
]);
$cmb->add_group_field( $phones, [
	'id'         => 'number',
	'type'       => 'text',
	'attributes' => [ 'placeholder' => esc_html__( 'Format: +1-401-555-1212', 'cpseo' ) ],
]);

$cmb->add_field([
	'id'   => 'cpseo_price_range',
	'type' => 'text',
	'name' => esc_html__( 'Price Range', 'cpseo' ),
	'desc' => esc_html__( 'The price range of the business, for example $$$.', 'cpseo' ),
	'dep'  => $company,
]);

$about_page    = Helper::get_settings( 'titles.cpseo_local_seo_about_page' );
$about_options = [ '' => __( 'Select Page', 'cpseo' ) ];
if ( $about_page ) {
	$about_options[ $about_page ] = get_the_title( $about_page );
}
$cmb->add_field([
	'id'         => 'cpseo_local_seo_about_page',
	'type'       => 'select',
	'options'    => $about_options,
	'name'       => esc_html__( 'About Page', 'cpseo' ),
	'desc'       => esc_html__( 'Select a page on your site where you want to show the LocalBusiness meta data.', 'cpseo' ),
	'attributes' => ( 'data-s2-pages' ),
]);

$contact_page    = Helper::get_settings( 'titles.cpseo_local_seo_contact_page' );
$contact_options = [ '' => __( 'Select Page', 'cpseo' ) ];
if ( $contact_page ) {
	$contact_options[ $contact_page ] = get_the_title( $contact_page );
}
$cmb->add_field([
	'id'         => 'cpseo_local_seo_contact_page',
	'type'       => 'select',
	'options'    => $contact_options,
	'name'       => esc_html__( 'Contact Page', 'cpseo' ),
	'desc'       => esc_html__( 'Select a page on your site where you want to show the LocalBusiness meta data.', 'cpseo' ),
	'attributes' => ( 'data-s2-pages' ),
]);
