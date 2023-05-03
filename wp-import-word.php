<?php
/*
 * Plugin Name: Wordpress Import Word
 * Plugin URI: https://blog.waltersanti.info
 * Description: Import content from Word document
 * Author: WiTech
 * Author URI: https://waltersanti.info
 * Donate link: 
 * Version: 1.2
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