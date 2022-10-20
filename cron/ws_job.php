<?php

/**
 * WP Initialize
 */
require __DIR__ . "/../../../../wp-load.php";
include __DIR__ . "/../class/ws_import.php";
$upload_dir   = wp_upload_dir();
#var_dump($upload_dir);
#exit;
$file = $upload_dir['basedir']."/word/test.docx";
$read = new ws_import();
$data = array(
  'post_title' => 'prova 5',
  'post_content' => $read->read_docx($file)
);
echo $postID = $read->ws_insert($data);
$meta = array(
  'meta_title'=>'meta titolo',
  'meta_description' => 'meta description'
);
$read->ws_update_meta($postID, $meta);
