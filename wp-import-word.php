<?php
/**
* Plugin Name: Wordpress Import content from Word document
* Plugin URI: https://blog.waltersanti.info
* Description: Import content from Word document
* Author: WiTech
* Author URI: https://waltersanti.info
* Version: 1.0
*
* @package WC_Admin
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'WPIMPORTWORD_PLUGIN_PLUGIN', __FILE__ );
define( 'WPIMPORTWORD_PLUGIN_DIR', untrailingslashit( dirname( WPIMPORTWORD_PLUGIN_PLUGIN ) ) );


require_once WPIMPORTWORD_PLUGIN_DIR . '/includes/functions.php';