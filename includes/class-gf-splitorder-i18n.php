<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://ghostfiregaming.com/
 * @since      1.0.0
 *
 * @package    Gf_Splitorder
 * @subpackage Gf_Splitorder/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Gf_Splitorder
 * @subpackage Gf_Splitorder/includes
 * @author     Franz Ian Barrio <franz@morningstardigital.com.au>
 */
class Gf_Splitorder_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'gf-splitorder',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
