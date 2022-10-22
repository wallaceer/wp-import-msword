<?php

function ws_word_import() {
    add_menu_page(
        'Word Import',
        'Word Import',
        'manage_options',
        'wp-import-word',
        'wp_import_word',
        'dashicons-editor-paste-word',
        25
    );
}

add_action('admin_menu', 'ws_word_import');


function register_wpwordimport_plugin_scripts() {

    wp_register_style( 'wp-import-word-css', plugins_url( 'wp-import-word/assets/css/style.css' ) );

    wp_register_script( 'wp-import-word-js', plugins_url( 'wp-import-word/assets/js/functions.js' ) );

}



add_action( 'admin_enqueue_scripts', 'register_wpwordimport_plugin_scripts' );



function load_wpwordimport_plugin_scripts( $hook ) {

    // Load only on ?page=sample-page

    if( $hook != 'toplevel_page_wp-import-word' ) {

        return;

    }

    // Load style & scripts.

    wp_enqueue_style( 'wp-import-word-css' );

    wp_enqueue_script( 'wp-import-word-js' );

}



add_action( 'admin_enqueue_scripts', 'load_wpwordimport_plugin_scripts' );




function wp_import_word() {
    ?>
    <h1>
        <?php esc_html_e( 'Welcome to my custom admin page.', 'my-plugin-textdomain' ); ?>
    </h1>
    <!-- (B) FILE DROP ZONE -->
    <div id="wp-import-word"></div>

    <!-- (C) ATTACH -->
    <script>
        ddup.init({
            target : document.getElementById("wp-import-word"), // target html <div>
            action : "../wp-content/plugins/wp-import-word/includes/ws_upload.php", // server-side upload handler
            data : { key : "value" } // optional, extra post data
        });
    </script>
    <?php
}