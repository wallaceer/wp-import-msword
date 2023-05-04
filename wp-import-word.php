<?php
/*
 * Plugin Name: Wordpress Import Word
 * Plugin URI: https://blog.waltersanti.info
 * Description: Import content from Word document
 * Author: WiTech
 * Author URI: https://waltersanti.info
 * Donate link: https://www.paypal.com/donate/?business=4UKWLJY2L4CN2&no_recurring=0&item_name=With+a+donation+you+can+support+the+GitHub+project+%22WP+import+Word%22&currency_code=EUR
 * Version: 1.3
 * Requires at least: 4.5
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
add_action( 'init', 'wpimportword_load_textdomain' );
function wpimportword_load_textdomain() {
    load_plugin_textdomain( 'wpimportword', FALSE, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
}

/**
 * Plugin update checker
 */
require dirname(__FILE__) . '/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/wallaceer/wp-import-word',
	__FILE__,
	'wp-import-word'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('production');

//Optional: If you're using a private repository, specify the access token like this:
$myUpdateChecker->setAuthentication('your-token-here');