#!/usr/bin/env php
<?php

define( 'ABSPATH', 'heeeey' );

/**
 * Stub of __().
 *
 * @param string $s String to passthru.
 * @return string
 */
function __( $s ) : string { return $s; }

/**
 * Stub of get_option.
 *
 * @return string // Always 'yes'.
 */
function get_option() : string { return 'yes'; }

// Autoloading.
require_once dirname( __FILE__ ) . '/../../../vendor/autoload.php';
require_once dirname( __FILE__ ) . '/../../../../woocommerce/includes/admin/settings/class-wc-settings-page.php';

use WebDevStudios\CCForWoo\Utility\Cypress\WooTabFixtures;

$fixtures = [
	'get_store_information_settings' => 'contact-settings.json',
	'get_customer_data_settings'     => 'consumer-settings.json',
];

foreach ( $fixtures as $method => $file ) {
	$fixture          = new WooTabFixtures( $method );
	$file_path        = "./cypress/fixtures/{$file}";
	$contact_settings = $fixture->get_data();
	if ( ! write_fixture_data( $file, $contact_settings ) ) {
		throw new \Exception( "Could not write {$file_path}!" );
	}

	echo "Success: wrote {$file_path} fixtures!" . PHP_EOL;
}

/**
 * Writes the given data to a Cypress fixture.
 *
 * @since 2019-03-18
 * @author Zach Owen <zach@webdevstudios>
 * @param string $file The filename to write the data to.
 * @param array  $data The data to write to the file.
 * @throws \Exception If the directory can't be written to.
 * @throws \Exception If the file cannot be opened.
 * @throws \Exception If the file cannot be written.
 * @return bool
 */
function write_fixture_data( $file, $data ) : bool {
	static $cypress_fixtures = '/../fixtures';

	$destination = dirname( __FILE__ ) . "{$cypress_fixtures}";

	if ( ! is_writable( $destination ) ) {
		throw new \Exception( "Could not write to directory {$destination}!" );
	}

	$destination_file = "{$destination}/{$file}";

	$fp = fopen( $destination_file, 'w' );

	if ( false === $fp ) {
		throw new \Exception( "Could not open {$destination_file} for writing!");
	}

	$written = fwrite( $fp, json_encode( $data, JSON_PRETTY_PRINT ) );

	if ( false === $written ) {
		throw new \Exception( "Could not write to {$destination_file}!" );
	}

	fclose( $fp );

	return true;
}
