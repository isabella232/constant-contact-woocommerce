<?php
namespace WebDevStudios\CCForWoo\Test\Settings;

use PHPUnit\Framework\TestCase;
use WebDevStudios\CCForWoo\Settings\SettingsModel;
use WebDevStudios\CCForWoo\Settings\SettingsValidator;

/**
 * Class SettingsValidatorTest
 *
 * @group SettingsValidator
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package ConstantContact\WooCommerce\Test\Settings
 * @since   2019-03-07
 */
class SettingsValidatorTest extends TestCase {
	/**
	 * Assert that settings are valid when admins select to import historical data and confirm their permission to e-mail customers.
	 *
	 * @test
	 */
	public function settings_are_valid_if_all_settings_pass_validation() {
		$settings = new SettingsModel(
			'Ilana',
			'Glazer',
			'555-555-5555',
			'Deals! Deals! Deals!',
			'$',
			'us',
			'ilana@dealsdealsdeals.com',
			'yes',
			'yes'
		);

		$validator = new SettingsValidator( $settings );

		$this->assertTrue( $validator->is_valid() );
	}

	/**
	 * Assert that settings are valid when admins select not to import historical data.
	 *
	 * @test
	 */
	public function settings_are_invalid_if_store_owner_does_not_affirm_consent_to_market() {
		$settings = new SettingsModel(
			'Abbi',
			'Jacobson',
			'555-555-5555',
			'Solstice',
			'$',
			'us',
			'cleaner@solstice.com',
			'no',
			'no'
		);

		$validator = new SettingsValidator( $settings );

		$this->assertFalse( $validator->is_valid() );
	}

	/**
	 * Historical data and user consent options should be the same value.
	 *
	 * @test
	 */
	public function settings_are_invalid_if_import_historical_data_and_user_consent_are_mismatched() {
		$settings = new SettingsModel(
			'Abbi',
			'Jacobson',
			'555-555-5555',
			'Solstice',
			'$',
			'us',
			'cleaner@solstice.com',
			'yes',
			'no'
		);

		$validator = new SettingsValidator( $settings );

		$this->assertFalse( $validator->is_valid() );
	}

	/**
	 * First name is required, so the validator should fail.
	 *
	 * @test
	 */
	public function settings_are_invalid_if_first_name_is_empty() {
		$settings = new SettingsModel(
			'',
			'Glazer',
			'555-555-5555',
			'Deals! Deals! Deals!',
			'$',
			'us',
			'ilana@dealsdealsdeals.com',
			'yes',
			'yes'
		);

		$validator = new SettingsValidator( $settings );

		$this->assertFalse( $validator->is_valid() );
	}

	/**
	 * Last name is a required field.
	 *
	 * @test
	 */
	public function settings_are_invalid_if_last_name_is_empty() {
		$settings = new SettingsModel(
			'Ilana',
			'',
			'555-555-5555',
			'Deals! Deals! Deals!',
			'$',
			'us',
			'ilana@dealsdealsdeals.com',
			'yes',
			'yes'
		);

		$validator = new SettingsValidator( $settings );

		$this->assertFalse( $validator->is_valid() );
	}

	/**
	 * Phone number is a required field.
	 *
	 * @test
	 */
	public function settings_are_invalid_if_phone_number_is_empty() {
		$settings = new SettingsModel(
			'Ilana',
			'Glazer',
			'',
			'Deals! Deals! Deals!',
			'$',
			'us',
			'ilana@dealsdealsdeals.com',
			'yes',
			'yes'
		);

		$validator = new SettingsValidator( $settings );

		$this->assertFalse( $validator->is_valid() );
	}

	/**
	 * Phone number is a required field.
	 *
	 * @test
	 */
	public function settings_are_valid_if_phone_number_starts_with_plus() {
		$settings = new SettingsModel(
			'Ilana',
			'Glazer',
			'+15553331234',
			'Deals! Deals! Deals!',
			'$',
			'us',
			'ilana@dealsdealsdeals.com',
			'yes',
			'yes'
		);

		$validator = new SettingsValidator( $settings );

		$this->assertTrue( $validator->is_valid() );
	}

	/**
	 * Phone number is a required field.
	 *
	 * @test
	 */
	public function settings_are_invalid_if_phone_number_has_plus_after_first_character() {
		$settings = new SettingsModel(
			'Ilana',
			'Glazer',
			'1555+3331234',
			'Deals! Deals! Deals!',
			'$',
			'us',
			'ilana@dealsdealsdeals.com',
			true,
			true
		);

		$validator = new SettingsValidator( $settings );

		$this->assertFalse( $validator->is_valid() );
	}

	/**
	 * Company name is a required field.
	 *
	 * @test
	 */
	public function settings_are_invalid_if_company_name_is_empty() {
		$settings = new SettingsModel(
			'Ilana',
			'Glazer',
			'555-555-5555',
			'',
			'$',
			'us',
			'ilana@dealsdealsdeals.com',
			'yes',
			'yes'
		);

		$validator = new SettingsValidator( $settings );

		$this->assertFalse( $validator->is_valid() );
	}

	/**
	 * @test
	 */
	public function settings_are_invalid_if_country_code_is_empty() {
		$settings = new SettingsModel(
			'Ilana',
			'Glazer',
			'555-555-5555',
			'Deals! Deals! Deals!',
			'$',
			'',
			'ilana@dealsdealsdeals.com',
			'yes',
			'yes'
		);

		$validator = new SettingsValidator( $settings );

		$this->assertFalse( $validator->is_valid() );
	}

	/**
	 * @test
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 */
	public function settings_are_invalid_if_email_is_empty() {
		$settings = new SettingsModel(
			'Ilana',
			'Glazer',
			'555-555-5555',
			'Deals! Deals! Deals!',
			'$',
			'us',
			'',
			'yes',
			'yes'
		);

		$validator = new SettingsValidator( $settings );

		$this->assertFalse( $validator->is_valid() );
	}

	/**
	 * @test
	 */
	public function settings_are_valid_if_phone_number_has_no_hyphens() {
		$settings = new SettingsModel(
			'Ilana',
			'Glazer',
			'5555555555',
			'Deals! Deals! Deals!',
			'$',
			'us',
			'ilana@dealsdealsdeals.com',
			'yes',
			'yes'
		);

		$validator = new SettingsValidator( $settings );

		$this->assertTrue( $validator->is_valid() );
	}

	/**
	 * @test
	 */
	public function settings_are_invalid_if_phone_number_contains_nonnumeric_characters() {
		$settings = new SettingsModel(
			'Ilana',
			'Glazer',
			'555-55-ILANA',
			'Deals! Deals! Deals!',
			'$',
			'us',
			'ilana@dealsdealsdeals.com',
			'yes',
			'yes'
		);

		$validator = new SettingsValidator( $settings );

		$this->assertFalse( $validator->is_valid() );
	}

	/**
	 * @test
	 */
	public function settings_are_invalid_if_email_is_missing_at_sign() {
		$settings = new SettingsModel(
			'Ilana',
			'Glazer',
			'555-555-5555',
			'Deals! Deals! Deals!',
			'$',
			'us',
			'ilanadealsdealsdeals.com',
			'yes',
			'yes'
		);

		$validator = new SettingsValidator( $settings );

		$this->assertFalse( $validator->is_valid() );
	}
}
