<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              omark.me
 * @since             2.1.23
 * @package           Ultimate_Facebook_Reviews
 *
 * @wordpress-plugin
 * Plugin Name:       Ultimate Facebook Reviews - WordPress Plugin
 * Plugin URI:        ultimate-facebook-reviews
 * Description:       Dispaly your Facebook pages Reviews in a beautiful way with a lot of options to customize the reviews as you like, in a slider facebook reviews or a regular facebook reviews, create unlimited shortcodes, widgets and wp rest api urls.
 * Version:           2.1.3
 * Author:            Omar Kasem,OmarK.me
 * Author URI:        http://www.omark.me
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ultimate-facebook-reviews
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('UFR_PLUGIN_VERSION','2.1.3');
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ultimate-facebook-reviews-activator.php
 */
function activate_ultimate_facebook_reviews() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ultimate-facebook-reviews-activator.php';
	Ultimate_Facebook_Reviews_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ultimate-facebook-reviews-deactivator.php
 */
function deactivate_ultimate_facebook_reviews() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ultimate-facebook-reviews-deactivator.php';
	Ultimate_Facebook_Reviews_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ultimate_facebook_reviews' );
register_deactivation_hook( __FILE__, 'deactivate_ultimate_facebook_reviews' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/libs/vendor/autoload.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-ultimate-facebook-reviews.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ultimate_facebook_reviews() {

	$plugin = new Ultimate_Facebook_Reviews();
	$plugin->run();

}
run_ultimate_facebook_reviews();
