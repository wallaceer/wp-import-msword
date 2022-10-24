<?php

function ws_word_import() {
    add_menu_page(
        'Word Import',
        'Word Import',
        'read',
        'wp-import-word',
        'wp_import_word',
        'dashicons-editor-paste-word',
        25
    );

    add_submenu_page( 'wp-import-word', 'Config WP Import Word', 'Configuration', 'manage_options', 'wp-import-word-config', 'wp_import_word_config' );
    add_submenu_page( 'wp-import-word', 'Logs WP Import Word', 'Logs', 'manage_options', 'wp-import-word-log', 'wp_import_word_log' );
}
add_action('admin_menu', 'ws_word_import');


function register_wpwordimport_plugin_scripts() {

    wp_register_style( 'wp-import-word-css', plugins_url( 'wp-import-word/assets/css/style.css' ) );

    wp_register_script( 'wp-import-word-js', plugins_url( 'wp-import-word/assets/js/functions.js' ) );

}
add_action( 'admin_enqueue_scripts', 'register_wpwordimport_plugin_scripts' );



function load_wpwordimport_plugin_scripts( $hook ) {

    // Load only on ?page=sample-page
   # if( $hook != 'toplevel_page_wp-import-word' ) {
    #    return;
   # }

    // Load style & scripts.
    wp_enqueue_style( 'wp-import-word-css' );
    wp_enqueue_script( 'wp-import-word-js' );

}
add_action( 'admin_enqueue_scripts', 'load_wpwordimport_plugin_scripts' );


function wp_import_word() {
    ?>
    <h1>
        <?php esc_html_e( 'Word Import', 'wp-import-word' ); ?>
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


function wp_import_word_config(){
    ?>
    <h1>
        <?php esc_html_e( 'Word Import Configuration', 'wp-import-word-config' ); ?>
    </h1>
    - directory di destinazione
    - separatore campi per il parsing del documento
    E' necessario non solo definire il separatore ma anceh la posizione di ogni campo. Ad es.
    Posizione 0 = titolo articolo
    Posizione 1 = descrizione articolo
    ecc..
    - stato post (draft, published, ...)

    <?php
}

function wp_import_word_log(){
    ?>
    <h1>
        <?php esc_html_e( 'Word Import Logs', 'wp-import-word-log' ); ?>
    </h1>
    <div class="log">
        <div class="scrollable-content">
    <?php
    $log = new ws_log();
    $log->logRead();
    $array_content = preg_split("/\r\n|\n|\r/", $log->filecontent);
    foreach($array_content as $row_content) echo $row_content."<br />";
    ?>
        </div>
    </div>
<?php
}