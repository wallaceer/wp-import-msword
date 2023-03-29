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
    add_submenu_page( 'wp-import-word', 'Doc WP Import Word', 'Help', 'manage_options', 'wp-import-word-doc', 'wp_import_word_doc' );

}
add_action('admin_menu', 'ws_word_import');


function register_wpwordimport_plugin_scripts() {

    wp_register_style( 'wp-import-word-css', plugins_url( 'wp-import-word/assets/css/style.css' ) );
    wp_register_script( 'wp-import-word-js', plugins_url( 'wp-import-word/assets/js/functions.js' ) );
    wp_register_script( 'wp-import-word-js2', plugins_url( 'wp-import-word/assets/js/sfunctions.js' ) );

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
        <?php esc_html_e( 'Word Import', 'wp-import-word' ); ?>
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
                <button id="button_create" value="btn_create" name="button_create" class="button"><?php echo __('Create Post')?></button>
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
        <?php esc_html_e( 'Word Import Configuration', 'wp-import-word-config' ); ?>
    </h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('wp-import-word-settings');
        do_settings_sections('wp-import-word-settings');
        ?>
        <fieldset>
            <table class="table">
                <tr>
                    <th scope="row" class="row"><?php echo __('Directory to save documents (only directory name)')?></th>
                    <td class="td">
                        <input type="text" name="wp_import_word_dir" value="<?php echo get_option( 'wp_import_word_dir' ); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="row"><?php echo __('Post type (Page/Post)') ?></th>
                    <td>
                        <select name="wp_import_word_post_type">
                            <option value="page" <?php if(get_option( 'wp_import_word_post_type' ) === 'page') echo 'selected="selected"'?>>Page</option>
                            <option value="post" <?php if(get_option( 'wp_import_word_post_type' ) === 'post') echo 'selected="selected"'?>>Post</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="row"><?php echo __('Post status after creation') ?></th>
                    <td>
                        <select name="wp_import_word_post_status">
                            <option value="Publish" <?php if(get_option( 'wp_import_word_post_status' ) === 'Publish') echo 'selected="selected"'?>>Publish</option>
                            <option value="Draft" <?php if(get_option( 'wp_import_word_post_status' ) === 'Draft') echo 'selected="selected"'?>>Draft</option>
                            <option value="Pending" <?php if(get_option( 'wp_import_word_post_status' ) === 'Pending') echo 'selected="selected"'?>>Pending</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="row"><?php echo __('Enable document parsing')?></th>
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
                                <th scope="row" class="row"><?php echo __('Post parent mapping')?></th>
                                <td>
                                    <textarea name="wp_import_word_post_parent"><?php echo get_option( 'wp_import_word_post_parent' ); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="row"><?php echo __('Character separator for document parsing')?></th>
                                <td>
                                    <input type="text" name="wp_import_word_separator" value="<?php echo get_option( 'wp_import_word_separator' ); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="row">
                                    <?php echo __('String structure')?><br />
                                    </th>
                                <td>
                                    <input type="text" name="wp_import_word_structure" value="<?php echo get_option( 'wp_import_word_structure' ); ?>" />
                                    <em><?php echo __('The field\'s position in the string structure define the field\'s position in the Word document. If is empty this configuration will not evaluate.')?></em>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="row"><?php echo __('String structure for ACF fields')?></th>
                                <td>
                                    <textarea name="wp_import_word_acf_mapping"><?php echo get_option( 'wp_import_word_acf_mapping' ); ?></textarea>
                                    <em><?php echo __('This json map the id of acf field with his relative acf name writed in String Structure. If is empty this configuration will not evaluate.')?></em>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="row"><?php echo __('Show Info/Error')?></th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="wp_import_word_alert" value="1" <?php if(get_option( 'wp_import_word_alert' ) == 1){?> checked="checked" <?php }?> />
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="row"><?php echo __('Show only errors')?></th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="wp_import_word_alert_only_error" value="1" <?php if(get_option( 'wp_import_word_alert_only_error' ) == 1){?> checked="checked" <?php }?> />
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="row"><?php echo __('Email to send import log')?></th>
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
        <?php esc_html_e( 'Word Import Logs', 'wp-import-word-log' ); ?>
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
        <?php esc_html_e( 'Word Import Documentation', 'wp-import-word-doc' ); ?>
    </h1>
    <div class="doc">
        <div class="scrollable-content">
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
