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
		return $this->import_preferences_match_permissions();
	}

	/**
	 * Compare settings to import historical data to store admin's confirmation of permission to e-mail customers.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 * @return bool
	 */
	private function import_preferences_match_permissions() : bool {
		return $this->settings->get_import_historical_data() === $this->settings->get_permission_confirmed();
	}
}
