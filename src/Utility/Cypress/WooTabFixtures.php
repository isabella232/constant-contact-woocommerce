<?php
/**
 * Fixture data generation class.
 *
 * Generates fixture data from the WooTab class.
 *
 * @since 2019-03-18
 * @author Zach Owen <zach@webdevstudios>
 * @package cc-woo
 */

namespace WebDevStudios\CCForWoo\Utility\Cypress;

use WebDevStudios\CCForWoo\View\Admin\WooTab;

/**
 * WooTabFixtures
 *
 * @since 2019-03-18
 */
class WooTabFixtures {
	/**
	 * List of non-input fields.
	 *
	 * @since 2019-03-18
	 * @var array
	 */
	public $non_input_fields = [
		'title',
		'button',
		'sectionend',
	];

	/**
	 * The name of the method to use.
	 *
	 * @since 2019-03-18
	 * @var string
	 */
	protected $method_name;

	/**
	 * The ReflectionMethod instance.
	 *
	 * @since 2019-03-18
	 * @var \ReflectionMethod
	 */
	protected $method;

	/**
	 * An instance of the WooTab
	 *
	 * @since 2019-03-18
	 * @var WooTab
	 */
	protected $tab;

	/**
	 * __construct
	 *
	 * @since 2019-03-18
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string $method_name The method name to use to generate fixture data.
	 */
	public function __construct( $method_name ) {
		$this->method_name = $method_name;
	}

	/**
	 * Setup the WooTab instance.
	 *
	 * @since 2019-03-18
	 * @author Zach Owen <zach@webdevstudios>
	 */
	protected function set_tab() {
		$this->tab = new WooTab();
	}

	/**
	 * Setup our tab and reflection method.
	 *
	 * @since 2019-03-18
	 * @author Zach Owen <zach@webdevstudios>
	 */
	protected function run() {
		$this->set_tab();
		$this->method = new \ReflectionMethod( get_class( $this->tab ), $this->method_name );
		$this->method->setAccessible( true );
	}

	/**
	 * Get data from the reflected method.
	 *
	 * @since 2019-03-18
	 * @author Zach Owen <zach@webdevstudios>
	 * @return array
	 */
	public function get_data() {
		$this->run();
		$data = array_filter( $this->method->invoke( $this->tab ), [ $this, 'filter_non_inputs' ] );
		return $this->format_field_data( $data );
	}

	/**
	 * Remove non-input type fields.
	 *
	 * @since 2019-03-18
	 * @author Zach Owen <zach@webdevstudios>
	 * @param array $value Field array for current field.
	 * @return bool
	 */
	public function filter_non_inputs( $value ) : bool {
		return ! in_array( $value['type'], $this->non_input_fields, true );
	}

	/**
	 * Format the field data.
	 *
	 * @since 2019-03-18
	 * @author Zach Owen <zach@webdevstudios>
	 * @param mixed $data Array of fields from the settings tab.
	 * @return array
	 */
	private function format_field_data( $data ) : array {
		$formatted = [];

		foreach ( $data as $field ) {
			$formatted[ $field['title'] ] = $field['id'];
		}

		return $formatted;
	}
}
