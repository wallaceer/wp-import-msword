<?php
require __DIR__ . "/../../../../wp-load.php";
require_once __DIR__ . "/../class/ws_files.php";
require_once __DIR__ . "/../class/ws_import.php";
require_once __DIR__ . "/../class/ws_log.php";
require_once __DIR__ . "/../class/ws_validate.php";
require_once __DIR__ . "/../class/ws_document.php";
require_once __DIR__ . '/ws_functions.php';


/**
 * Working files type
 */
$files_type_admitted = array('application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

/**
 * Load configurations data
 */
$wordDir = get_option('wp_import_word_dir');
$postStatus = get_option('wp_import_word_post_status');
$docParsing = get_option('wp_import_word_document_parsing');
$docSeparator = get_option('wp_import_word_separator');
$docStructure = get_option('wp_import_word_structure');
$docAlert = get_option('wp_import_word_alert');
$docAlertOnlyError = get_option('wp_import_word_alert_only_error');
$docEmail = get_option('wp_import_word_email');
$postType = get_option('wp_import_word_post_type');
$postParent = get_option('wp_import_word_post_parent');
$acfMapping = get_option('wp_import_word_acf_mapping');

$upload_dir   = wp_upload_dir();
/**
 * Working directory
 */
$dir = $upload_dir['basedir']."/".$wordDir."/";
if(!is_dir($dir)){
    mkdir($dir);
}

