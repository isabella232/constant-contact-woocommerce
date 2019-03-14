<?php
/**
 * Validation class for the SettingsModel.
 *
 * This class is used to verify that data submitted to the plugin settings are valid and correctly formatted.
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Settings
 * @since   2019-03-07
 */

namespace WebDevStudios\CCForWoo\Settings;

use WebDevStudios\CCForWoo\Utility\Validatable;

/**
 * Class SettingsValidator
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Settings
 * @since   2019-03-07
 */
class SettingsValidator implements Validatable {
	/**
	 * Instance of the SettingsModel.
	 *
	 * @var SettingsModel
	 * @since 2019-03-07
	 */
	private $settings;

	/**
	 * SettingsValidator constructor.
	 *
	 * @param SettingsModel $settings Instance of the SettingsModel.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 */
	public function __construct( SettingsModel $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Confirm whether data is valid.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 * @return bool
	 */
	public function is_valid(): bool {
		return (
			$this->store_owner_affirms_consent_to_market()
			&& $this->has_valid_name()
			&& $this->has_valid_phone()
			&& $this->has_valid_store_name()
			&& $this->has_valid_email()
			&& $this->has_valid_country_code()
		);
	}

	/**
	 * Compare settings to import historical data to store admin's confirmation of permission to e-mail customers.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 * @return bool
	 */
	private function store_owner_affirms_consent_to_market() : bool {
		return $this->settings->get_permission_confirmed();
	}

	/**
	 * Verify that the name is valid.
	 *
	 * @since 2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	private function has_valid_name() {
		return ! empty( trim( $this->settings->get_first_name() ) ) && ! empty( trim( $this->settings->get_last_name() ) );
	}

	/**
	 * Verify that the store name is valid.
	 *
	 * @since 2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	private function has_valid_store_name() {
		return ! empty( trim( $this->settings->get_store_name() ) );
	}

	/**
	 * Verify that the email address is valid.
	 *
	 * @since 2019-03-08s
	 * @author Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	private function has_valid_email() {
		return ! empty( filter_var( $this->settings->get_email_address(), FILTER_VALIDATE_EMAIL ) );
	}

	/**
	 * Verify that the phone number is valid.
	 *
	 * @since 2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	private function has_valid_phone() {
		return ! empty( filter_var( $this->settings->get_phone_number(), FILTER_SANITIZE_STRING ) );
	}

	/**
	 * Validates the Country Code field.
	 *
	 * @since 2019-03-11
	 * @author Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	private function has_valid_country_code() {
		return ! empty( $this->settings->get_country_code() );
	}
}
