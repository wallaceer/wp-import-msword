<?php
/*
 * Plugin Name: Wordpress Import Word
 * Plugin URI: https://blog.waltersanti.info
 * Description: Import content from Word document
 * Author: WiTech
 * Author URI: https://waltersanti.info
 * Version: 2.2
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Text Domain: wpimportword
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

include __DIR__ . "/includes/ws_load.php";

/**
 * Load the plugin textdomain for localisation
 * @since 2.0.0
 */
function wp_import_load_plugin_textdomain() {
    load_plugin_textdomain( 'wpimportword', FALSE, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'wp_import_load_plugin_textdomain' );