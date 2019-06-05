<?php

namespace WebDevStudios\CCForWoo\Test\View\Admin;

use WP_Mock\Tools\TestCase;
use WebDevStudios\CCForWoo\View\Admin\Field\ImportHistoricalData;

class ImportHistoricalDataTest extends TestCase {
	/**
 	 * Tests that the description is empty if the field is readonly.
 	 *
 	 * @test
 	 */
	public function desription_is_empty_for_true_value() {
		$object                 = new ImportHistoricalData();
		$get_description_method = new \ReflectionMethod( $object, 'get_description' );
		$get_description_method->setAccessible( true );

		\WP_Mock::userFunction( 'get_option', [
			'return' => 'true',
		] );

		$this->assertTrue( 0 === strlen( $get_description_method->invoke( $object ) ) );
	}

	/**
	 * Tests that the description is populated when the option field is "false".
	 *
	 * @test
	 */
	public function description_is_not_empty_for_true_value() {
		$object                 = new ImportHistoricalData();
		$get_description_method = new \ReflectionMethod( $object, 'get_description' );
		$get_description_method->setAccessible( true );

		\WP_Mock::userFunction( 'get_option', [
			'return' => 'false',
		] );

		$this->assertTrue( 0 < strlen( $get_description_method->invoke( $object ) ) );
	}

	/**
	 * Tests that the field is readonly when the option is "true".
	 *
	 * @test
	 */
	public function field_is_readonly_for_true_value() {
		$object             = new ImportHistoricalData();
		$is_readonly_method = new \ReflectionMethod( $object, 'is_readonly' );
		$is_readonly_method->setAccessible( true );

		\WP_Mock::userFunction( 'get_option', [
			'return' => 'true',
		] );

		$this->assertTrue( $is_readonly_method->invoke( $object ) );
	}

	/**
	 * Tests that the field is readonly when the option is "true".
	 *
	 * @test
	 */
	public function field_is_not_readonly_for_false_value() {
		$object             = new ImportHistoricalData();
		$is_readonly_method = new \ReflectionMethod( $object, 'is_readonly' );
		$is_readonly_method->setAccessible( true );

		\WP_Mock::userFunction( 'get_option', [
			'return' => 'false',
		] );

		$this->assertFalse( $is_readonly_method->invoke( $object ) );
	}
}
