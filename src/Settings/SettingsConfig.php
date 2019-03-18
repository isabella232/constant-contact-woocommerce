<?php
/**
 * Settings page configuration object.
 *
 * Used to configure settings for the \WebDevStudios\CCForWoo\Settings\SettingsTab class.
 *
 * @since 0.0.1
 * @author Zach Owen <zach@webdevstudios>
 * @package wds-settings
 */

namespace WebDevStudios\CCForWoo\Settings;

/**
 * Settings Configuration class.
 *
 * @since 0.0.1
 */
class SettingsConfig {
	/**
	 * The option group for these settings.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	protected $option_group;

	/**
	 * The page to display the settings on.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	protected $page;

	/**
	 * Create the settings configuration.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string $option_group The option group ID.
	 * @param string $page The page to display the settings on.
	 */
	public function __construct( string $option_group, string $page ) {
		$this->option_group = $option_group;
		$this->page         = $page;
	}

	/**
	 * Get the option group.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @return string
	 */
	public function get_option_group() : string {
		return $this->option_group;
	}

	/**
	 * Get the page the options are displayed on.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @return string
	 */
	public function get_page() : string {
		return $this->page;
	}
}
