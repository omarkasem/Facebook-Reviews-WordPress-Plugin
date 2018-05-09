<?php

/**
 * Fired during plugin activation
 *
 * @link       omark.me
 * @since      1.0.0
 *
 * @package    Ultimate_Facebook_Reviews
 * @subpackage Ultimate_Facebook_Reviews/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ultimate_Facebook_Reviews
 * @subpackage Ultimate_Facebook_Reviews/includes
 * @author     Omar Kasem <omar.kasem207@gmail.com>
 */
class Ultimate_Facebook_Reviews_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		update_option('ufr_fb_app_id','110645242897641');
	}

}
