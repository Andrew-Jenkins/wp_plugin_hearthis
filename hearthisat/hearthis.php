<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://hearthis.at/
 * @since             1.0.0
 * @package           Hearthis
 *
 * @wordpress-plugin
 * Plugin Name:       hearthis.at
 * Contributors:      hearthis, dj_force
 * Donate link:       https://hearthis.at/
 * Plugin URI:        https://wordpress.org/plugins/hearthisat/
 * Tags:              hearthis, html5, player, sound, mp3, audio, shortcodes, music, widget
 * Description:       The hearthis.at plugin allows you to integrate a player widget from <a href="https://hearthis.at/" target="_blank">hearthis.at</a> into your Blog by using a Wordpress shortcodes.  Example: [hearthis]http://hearthis.at/shawne/shawne-stadtfest-chemnitz-31082013/[/hearthis]
 * Version:           1.0.2
 * Requires at least: 3.1
 * Tested up to:      4.3.2
 * Author:            Andreas Jenke / Benedikt Gro&szlig;
 * Author URI:        https://hearthis.at/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Stable tag:        stable
 * Text Domain:       Hearthis
 * Domain Path:       /languages
	 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('HEARTHIS_PLUGIN_FILE', plugin_basename(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-hearthis-activator.php
 */
function activate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hearthis-activator.php';
	Hearthis_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-hearthis-deactivator.php
 */
function deactivate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hearthis-deactivator.php';
	Hearthis_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_plugin_name' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-hearthis.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_hearthis() {

	$plugin = new Hearthis();
	$plugin->run();
}
run_hearthis();

