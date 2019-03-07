<?php
/**
 *
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package ConstantContact\WooCommerce\Api
 * @since   2019-03-07
 */

namespace ConstantContact\CCForWoo\Api;

/**
 * Class RequestHandler
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package ConstantContact\WooCommerce\Api
 * @since   2019-03-07
 */
class RequestHandler {
	private $url;





	public function settings_request( SettingsSubmitter $settings ) {
		$data = $settings->get_data();


	}
}