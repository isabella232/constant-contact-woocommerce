<?php
namespace WebDevStudios\CCForWoo\Test\Api;

use PHPUnit\Framework\TestCase;
use WebDevStudios\CCForWoo\Api\KeyManager;

/**
 * Class KeyManagerTest
 *
 * @group KeyManager
 * @author Zach Owen <zach@webdevstudios>
 * @package ConstantContact\WooCommerce\Test\Api
 * @since   2019-03-21
 */
class KeyManagerTest extends TestCase {
	/**
	 * Tests that SELECT queries don't pass query validation.
	 *
	 * @test
	 */
	public function query_is_invalid_if_select_query() {
		$query = 'SELECT * FROM foo';
		$this->assertFalse( $this->check_if_delete_query( $query ) );
	}

	/**
	 * Tests that INSERT queries don't pass query validation.
	 *
	 * @test
	 */
	public function query_is_invalid_if_insert_query() {
		$query = 'INSERT INTO foo (bar) VALUES("Baz")';
		$this->assertFalse( $this->check_if_delete_query( $query ) );
	}

	/**
	 * Tests that UPDATE queries don't pass query validation.
	 *
	 * @test
	 */
	public function query_is_invalid_if_update_query() {
		$query = 'UPDATE foo SET bar="Baz"';
		$this->assertFalse( $this->check_if_delete_query( $query ) );
	}

	/**
	 * Tests that DELETE queries DO pass query validation.
	 *
	 * @test
	 */
	public function query_is_valid_if_delete_query() {
		$query = 'DELETE FROM foo';
		$this->assertTrue( $this->check_if_delete_query( $query ) );
	}

	/**
	 * Tests that queries to tables other than Woo's API keys table are invalid.
	 *
	 * @test
	 */
	public function query_is_invalid_if_not_woo_api_query() {
		$query = 'DELETE FROM somewhere_else';
		$this->assertFalse( $this->check_if_woo_api_query( $query ) );
	}

	/**
	 * Tests that queries on Woo's API keys table are valid.
	 *
	 * @test
	 */
	public function query_is_valid_if_woo_api_query() {
		$query = 'DELETE FROM wp_woocommerce_api_keys';
		$this->assertTrue( $this->check_if_woo_api_query( $query ) );
	}

	/**
	 * Tests that the query as a whole meets our criteria for a valid query.
	 *
	 * @test
	 */
	public function query_is_revocation_query() {
		$query = 'DELETE FROM wp_woocommerce_api_keys';
		$object = new KeyManager();
		$method = new \ReflectionMethod( $object, 'is_cc_api_revocation_query' );

		$method->setAccessible( true );

		$this->assertTrue( $method->invoke( $object, $query ) );
	}

	/**
	 * Helper for testing queries against DELETE critera.
	 *
	 * @param mixed $query The query to test.
	 * @return bool
	 */
	private function check_if_delete_query( $query ) {
		$object = new KeyManager();
		$method = new \ReflectionMethod( $object, 'is_delete_query' );

		$method->setAccessible( true );

		return $method->invoke( $object, $query );
	}

	/**
	 * Helper to test the query against being a Woo API query.
	 *
	 * @param mixed $query The query to test.
	 * @return bool
	 */
	private function check_if_woo_api_query( $query ) {
		$object = new KeyManager();
		$method = new \ReflectionMethod( $object, 'is_woo_commerce_api_key_query' );

		$method->setAccessible( true );

		return $method->invoke( $object, $query );
	}
}
