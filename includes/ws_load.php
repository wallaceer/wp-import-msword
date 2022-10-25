<?php
require __DIR__ . "/../../../../wp-load.php";
require_once __DIR__ . "/../class/ws_files.php";
require_once __DIR__ . "/../class/ws_import.php";
require_once __DIR__ . "/../class/ws_log.php";
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
$docSeparator = get_option('wp_import_word_separator');
$docStructure = get_option('wp_import_word_structure');

$upload_dir   = wp_upload_dir();
/**
 * Working directory
 */
$dir = $upload_dir['basedir']."/".$wordDir."/";
if(!is_dir($dir)){
    mkdir($dir);
}

