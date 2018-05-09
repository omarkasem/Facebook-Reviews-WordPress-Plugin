<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       omark.me
 * @since      1.0.0
 *
 * @package    Ultimate_Facebook_Reviews
 * @subpackage Ultimate_Facebook_Reviews/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ultimate_Facebook_Reviews
 * @subpackage Ultimate_Facebook_Reviews/includes
 * @author     Omar Kasem <omar.kasem207@gmail.com>
 */
class Ultimate_Facebook_Reviews_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ultimate-facebook-reviews',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
