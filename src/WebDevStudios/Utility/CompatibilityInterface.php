<?php
/**
 * Plugin Compatability Interface
 *
 * @since 0.0.1
 * @author Zach Owen <zach@webdevstudios>
 * @package wds-utility
 */

namespace WebDevStudios\Utility;

/**
 * Interface for Compatibility Checks
 *
 * @since 0.0.1
 */
interface CompatibilityInterface {
	/**
	 * Should determine if the given plugin is available to use.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	public function is_available() : bool;

	/**
	 * Should determine if the given plugin is compatible.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @param object $instance An instance of the plugin's class.
	 * @return bool
	 */
	public function is_compatible( $instance ) : bool;
}
