<?php
/**
 * WP Initialize
 */
require __DIR__ . "/../../../../wp-load.php";
include __DIR__ . "/../class/ws_files.php";
include __DIR__ . "/../class/ws_import.php";
$upload_dir   = wp_upload_dir();

/**
 * Working directory
 */
$dir = $upload_dir['basedir']."/word/";

/**
 * Loading class
 */
$file_c = new ws_files();
$read = new ws_import();

/**
 * Load file collection from working directory
 */
$files_collection = $file_c->ws_scandir($dir);
#var_dump($files_collection);exit;

/**
 * Create post for each document
 */
foreach($files_collection as $file){

    if($file['type'] === 'application/msword'){
        $content = $read->read_doc($dir.$file['name']);
    }
    elseif($file['type'] === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
        $content = $read->read_docx($dir.$file['name']);
    }

    $data = array(
        'post_title' => 'Post from document '.$file['name'],
        'post_content' => $content
    );
    /**
     * Create post from file
     */
    $read->ws_insert($data);
    if(isset($read->post_id)){
        /**
         * Set meta data from file
         */
        $meta = array(
            'meta_title'=>'meta titolo '.$file['name'],
            'meta_description' => 'meta description '.$file['name']
        );
        $read->ws_update_meta($read->post_id, $meta);

        /**
         * Delete file
         */
        $file_c->ws_delete_file($dir.$file['name']);

    }
    else{
        echo $read->error;
    }


}

