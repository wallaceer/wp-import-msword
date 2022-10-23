<?php
require __DIR__ . "/../../../../wp-load.php";
require_once __DIR__ . "/../class/ws_files.php";
require_once __DIR__ . "/../class/ws_import.php";
require_once __DIR__ . '/ws_functions.php';

$upload_dir   = wp_upload_dir();
/**
 * Working directory
 */
$dir = $upload_dir['basedir']."/word/";

/**
 * Working files type
 */
$files_type_admitted = array('application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');