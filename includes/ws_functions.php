<?php

function ws_word_import() {
    add_menu_page(
        __('Word Import', 'wpimportword'),
        __('Word Import','wpimportword'),
        'read',
        'wp-import-word',
        'wp_import_word',
        'dashicons-editor-paste-word',
        25
    );

    add_submenu_page( 'wp-import-word', __('Config WP Import Word', 'wpimportword'), __('Configuration', 'wpimportword'), 'manage_options', 'wp-import-word-config', 'wp_import_word_config' );
    add_submenu_page( 'wp-import-word', __('Logs WP Import Word', 'wpimportword'), __('Logs','wpimportword'), 'manage_options', 'wp-import-word-log', 'wp_import_word_log' );
    add_submenu_page( 'wp-import-word', __('Doc WP Import Word', 'wpimportword'), __('Help','wpimportword'), 'manage_options', 'wp-import-word-doc', 'wp_import_word_doc' );

}
add_action('admin_menu', 'ws_word_import');


function register_wpwordimport_plugin_scripts() {

    wp_register_style( 'wp-import-word-css', plugins_url( 'wp-import-word/assets/css/style.css' ) );
    wp_register_script( 'wp-import-word-js', plugins_url( 'wp-import-word/assets/js/functions.js' ) );
    wp_register_script( 'wp-import-word-js2', plugins_url( 'wp-import-word/assets/js/sfunctions.js' ) );

}
add_action( 'admin_enqueue_scripts', 'register_wpwordimport_plugin_scripts' );



function load_wpwordimport_plugin_scripts( $hook ) {

    // Load style & scripts.
    wp_enqueue_style( 'wp-import-word-css' );
    wp_enqueue_script( 'wp-import-word-js' );
    wp_enqueue_script( 'wp-import-word-js2' );

}
add_action( 'admin_enqueue_scripts', 'load_wpwordimport_plugin_scripts' );

/**
 * Upload files and import it
 * @return void
 */
function wp_import_word() {
    ?>
    <h1>
        <?php esc_html_e( 'Word Import', 'wpimportword' ); ?>
    </h1>
    <!-- (B) FILE DROP ZONE -->
    <div id="wp-import-word"></div>

    <!-- (C) ATTACH -->
    <script>
        ddup.init({
            target : document.getElementById("wp-import-word"), // target html <div>
            action : "../wp-content/plugins/wp-import-word/actions/ws_upload.php", // server-side upload handler
            data : { key : "value" } // optional, extra post data
        });
    </script>
    <div class="action_create_posts">
            <div id="button_create_posts">
                <button id="button_create" value="btn_create" name="button_create" class="button"><?php _e('Create Post', 'wpimportword')?></button>
                <input id="access_token" type="hidden" name="access_token" value="<?php echo wp_get_session_token(); ?>" />
            </div>
            <div id="result_create_posts" class="result_create_posts"></div>
    </div>
    <?php
}

/**
 * Admin actions
 */
function register_wp_import_word_settings(){
    register_setting('wp-import-word-settings', 'wp_import_word_dir');
    register_setting('wp-import-word-settings', 'wp_import_word_post_status');
    register_setting('wp-import-word-settings', 'wp_import_word_separator');
    register_setting('wp-import-word-settings', 'wp_import_word_structure');
    register_setting('wp-import-word-settings', 'wp_import_word_alert');
    register_setting('wp-import-word-settings', 'wp_import_word_alert_only_error');
    register_setting('wp-import-word-settings', 'wp_import_word_email');
    register_setting('wp-import-word-settings', 'wp_import_word_post_type');
    register_setting('wp-import-word-settings', 'wp_import_word_post_parent');
    register_setting('wp-import-word-settings', 'wp_import_word_document_parsing');
    register_setting('wp-import-word-settings', 'wp_import_word_acf_mapping');
}
add_action('admin_init', 'register_wp_import_word_settings');


/**
 * Plugin configuration form
 * @return void
 */
function wp_import_word_config(){
    ?>
    <h1>
        <?php _e( 'Word Import Configuration', 'wpimportword' ); ?>
    </h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('wp-import-word-settings');
        do_settings_sections('wp-import-word-settings');
        ?>
        <fieldset>
            <table class="table">
                <tr>
                    <th scope="row" class="row"><?php _e('Directory to save documents (only directory name)', 'wpimportword')?></th>
                    <td class="td">
                        <input type="text" name="wp_import_word_dir" value="<?php echo get_option( 'wp_import_word_dir' ); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="row"><?php _e('Post type (Page/Post)', 'wpimportword') ?></th>
                    <td>
                        <select name="wp_import_word_post_type">
                            <option value="page" <?php if(get_option( 'wp_import_word_post_type' ) === 'page') echo 'selected="selected"'?>><?php _e('Page', 'wpimportword')?></option>
                            <option value="post" <?php if(get_option( 'wp_import_word_post_type' ) === 'post') echo 'selected="selected"'?>><?php _e('Post', 'wpimportword')?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="row"><?php _e('Post status after creation', 'wpimportword') ?></th>
                    <td>
                        <select name="wp_import_word_post_status">
                            <option value="Publish" <?php if(get_option( 'wp_import_word_post_status' ) === 'Publish') echo 'selected="selected"'?>><?php _e('Publish', 'wpimportword' )?></option>
                            <option value="Draft" <?php if(get_option( 'wp_import_word_post_status' ) === 'Draft') echo 'selected="selected"'?>><?php _e('Draft', 'wpimportword')?></option>
                            <option value="Pending" <?php if(get_option( 'wp_import_word_post_status' ) === 'Pending') echo 'selected="selected"'?>><?php _e('Pending', 'wpimportword')?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="row"><?php _e('Enable document parsing', 'wpimportword')?></th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="wp_import_word_document_parsing" value="1" <?php if(get_option( 'wp_import_word_document_parsing' ) == 1){?> checked="checked" <?php }?> onclick="parse_document_config()" />
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table id="parse_document_config" style="display:inline;">
                            <tr>
                                <th scope="row" class="row"><?php _e('Post parent mapping', 'wpimportword')?></th>
                                <td>
                                    <textarea name="wp_import_word_post_parent"><?php echo get_option( 'wp_import_word_post_parent' ); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="row"><?php _e('Character separator for document parsing', 'wpimportword')?></th>
                                <td>
                                    <input type="text" name="wp_import_word_separator" value="<?php echo get_option( 'wp_import_word_separator' ); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="row">
                                    <?php _e('String structure', 'wp-import-word')?><br />
                                    </th>
                                <td>
                                    <input type="text" name="wp_import_word_structure" value="<?php echo get_option( 'wp_import_word_structure' ); ?>" />
                                    <em><?php _e('The position of field in the string structure define the field\'s position in the Word document. If is empty this configuration will not evaluate.', 'wpimportword')?></em>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="row"><?php _e('String structure for ACF fields', 'wpimportword')?></th>
                                <td>
                                    <textarea name="wp_import_word_acf_mapping"><?php echo get_option( 'wp_import_word_acf_mapping' ); ?></textarea>
                                    <em><?php _e('This json map the id of acf field with his relative acf name writed in String Structure. If is empty this configuration will not evaluate.')?></em>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="row"><?php _e('Show only errors', 'wpimportword')?></th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="wp_import_word_alert_only_error" value="1" <?php if(get_option( 'wp_import_word_alert_only_error' ) == 1){?> checked="checked" <?php }?> />
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="row"><?php _e('Send report Email', 'wpimportword')?></th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="wp_import_word_alert" value="1" <?php if(get_option( 'wp_import_word_alert' ) == 1){?> checked="checked" <?php }?> />
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="row"><?php _e('Email address for report', 'wpimportword')?></th>
                    <td>
                        <input type="email" name="wp_import_word_email" value="<?php echo get_option( 'wp_import_word_email' ); ?>" />
                    </td>
                </tr>
            </table>
        </fieldset>

        <?php submit_button(); ?>
    </form>
    <?php
}

/**
 * Get post parent ID from Macroarea
 * @param $macroarea
 * @param $post_parent
 * @return mixed
 */
function get_post_parent_from_macroarea($macroarea, $post_parent){
    $_pp = json_decode($post_parent, true);
    return $_pp[$macroarea] ?? 0;
}

/**
 * Get ACF fields configuration
 * @param $acfconf
 * @return array
 */
function ws_get_acf_from_config($acfconf){
    return json_decode($acfconf, true);
}

/**
 * Read log content
 * @return void
 */
function wp_import_word_log(){
    ?>
    <h1>
        <?php esc_html_e( 'Word Import Logs', 'wpimportword' ); ?>
    </h1>
    <div class="log">
        <div class="scrollable-content">
            <?php
            $log = new ws_log();
            $log->logRead();
            $array_content = preg_split("/\r\n|\n|\r/", $log->filecontent);
            foreach($array_content as $row_content) echo $log->formatLog($row_content)."<br />";
            ?>
        </div>
    </div>
    <?php
}

/**
 * Read log content
 * @return void
 */
function wp_import_word_doc(){
    ?>
    <h1>
        <?php esc_html_e( 'Word Import Documentation', 'wpimportword' ); ?>
    </h1>
    <div class="doc">
        <div class="">
            <?php
            $documentFile = plugin_dir_path( __FILE__ ) . '/../README.md' ;
            $fileDoc = fopen($documentFile ,"r");
            $docFilecontent = fread($fileDoc, filesize($documentFile));
            fclose($fileDoc);
            $array_content = preg_split("/\r\n|\n|\r/", $docFilecontent);
            foreach($array_content as $row_content) echo $row_content."<br />";
            ?>
        </div>
    </div>
    <?php
}


/**
 * Load macroarea details
 * @return array(object(codice, macroarea))
 */
function wp_get_data_macroarea($code=NULL) {
    global $wpdb;

    $table_name = $wpdb->prefix . "macroarea";
    $query = "SELECT * FROM $table_name where 1";
    $query .= $code !== NULL ? " and codice = '".$code."'" : '';
    $query .= ';';

    $macroareaData = $wpdb->get_results( "$query" );

    return $macroareaData;
}
