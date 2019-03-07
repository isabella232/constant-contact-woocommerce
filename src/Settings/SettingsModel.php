<?php
/**
 * Value object for the Settings page submission.
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package ConstantContact\WooCommerce\Settings
 * @since   2019-03-07
 */

namespace ConstantContact\WooCommerce\Settings;

/**
 * Class SettingsModel
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package ConstantContact\WooCommerce\Settings
 * @since   2019-03-07
 */
class SettingsModel {
	/**
	 * Store admin's first name.
	 *
	 * @var string
	 * @since 2019-03-07
	 */
	private $first_name;

	/**
	 * Store admin's last name.
	 *
	 * @var string
	 * @since 2019-03-07
	 */
	private $last_name;

	/**
	 * Store admin's phone number.
	 *
	 * @var string
	 * @since 2019-03-07
	 */
	private $phone_number;

	/**
	 * Name of the store.
	 *
	 * @var string
	 * @since 2019-03-07
	 */
	private $store_name;

	/**
	 * Store currency type.
	 *
	 * @var string
	 * @since 2019-03-07
	 */
	private $currency;

	/**
	 * Store country code.
	 *
	 * @var string
	 * @since 2019-03-07
	 */
	private $country_code;

	/**
	 * Store admin's e-mail address.
	 *
	 * @var string
	 * @since 2019-03-07
	 */
	private $email_address;

	/**
	 * SettingsModel constructor.
	 *
	 * @param string $first_name    Store admin's first name.
	 * @param string $last_name     Store admin's last name.
	 * @param string $phone_number  Store admin's phone number.
	 * @param string $store_name    Name of the store.
	 * @param string $currency      Currency of the store.
	 * @param string $country_code  Country code of the store.
	 * @param string $email_address Store admin's e-mail address.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 */
	public function __construct(
		$first_name,
		$last_name,
		$phone_number,
		$store_name,
		$currency,
		$country_code,
		$email_address
	) {
		$this->first_name    = $first_name;
		$this->last_name     = $last_name;
		$this->phone_number  = $phone_number;
		$this->store_name    = $store_name;
		$this->currency      = $currency;
		$this->country_code  = $country_code;
		$this->email_address = $email_address;
	}

	/**
	 * Get the store admin's first name.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 * @return string
	 */
	public function get_first_name(): string {
		return $this->first_name;
	}

	/**
	 * Get the store admin's last name.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 * @return string
	 */
	public function get_last_name(): string {
		return $this->last_name;
	}

	/**
	 * Get the store's phone number.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 * @return string
	 */
	public function get_phone_number(): string {
		return $this->phone_number;
	}

	/**
	 * Get the store name.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 * @return string
	 */
	public function get_store_name(): string {
		return $this->store_name;
	}

	/**
	 * Get the store currency.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 * @return string
	 */
	public function get_currency(): string {
		return $this->currency;
	}

	/**
	 * Get the store's country code.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 * @return string
	 */
	public function get_country_code(): string {
		return $this->country_code;
	}

	/**
	 * Get the store admin's e-mail address.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 * @return string
	 */
	public function get_email_address(): string {
		return $this->email_address;
	}
}
