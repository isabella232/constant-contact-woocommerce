<?php
namespace WebDevStudios\CCForWoo\Test\Settings;

use PHPUnit\Framework\TestCase;
use WebDevStudios\CCForWoo\Settings\SettingsModel;
use WebDevStudios\CCForWoo\Settings\SettingsValidator;

/**
 * Class SettingsValidatorTest
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Test\Settings
 * @since   2019-03-07
 */
class SettingsValidatorTest extends TestCase {
	/**
	 * Assert that settings are valid when admins select to import historical data and confirm their permission to e-mail customers.
	 *
	 * @test
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 */
	public function settings_are_valid_if_import_historical_data_and_permission_confirmed_are_both_true() {
		$settings = new SettingsModel(
			'Ilana',
			'Glazer',
			'555-555-5555',
			'Deals! Deals! Deals!',
			'$',
			'us',
			'ilana@dealsdealsdeals.com',
			true,
			true
		);

		$validator = new SettingsValidator( $settings );

		$this->assertTrue( true, $validator->is_valid() );
	}

	/**
	 * Assert that settings are valid when admins select not to import historical data.
	 *
	 * @test
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 * @return void
	 */
	public function settings_are_valid_if_import_historical_data_is_false() {
		$settings = new SettingsModel(
			'Abbi',
			'Jacobson',
			'555-555-5555',
			'Solstice',
			'$',
			'us',
			'cleaner@solstice.com',
			false,
			false
		);

		$validator = new SettingsValidator( $settings );

		$this->assertTrue( true, $validator->is_valid() );
	}
}
