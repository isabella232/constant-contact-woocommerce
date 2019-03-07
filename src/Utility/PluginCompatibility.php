<?php
/**
 * Abstract plugin compatibility class.
 *
 * @since 0.0.1
 * @author Zach Owen <zach@webdevstudios>
 * @package cc-woo
 */

namespace WebDevStudios\CCForWoo\Utility;

/**
 * Plugin Compatibility Class
 *
 * @since 0.0.1
 */
abstract class PluginCompatibility implements CompatibilityInterface {
	/**
	 * The classname we'll be using for compatibility testing.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	protected $classname = '';

	/**
	 * Construct our compatibility checker with the main plugin class.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string $classname The classname to use for testing.
	 */
	public function __construct( string $classname ) {
		$this->classname = $classname;
	}
}
