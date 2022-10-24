<?php
/**
 * WP Initialize
 */
include __DIR__ . "/../includes/ws_load.php";


/**
 * Loading class
 */
$file_c = new ws_files();
$read = new ws_import();
$log = new ws_log();

/**
 * Load file collection from working directory
 */
$files_collection = $file_c->ws_scandir($dir, $files_type_admitted);
#var_dump($files_collection);exit;

/**
 * Create post for each document
 */
foreach($files_collection as $file){

    if(in_array($file['type'], $files_type_admitted)) {

        if ($file['type'] === 'application/msword') {
            $content = $read->read_doc($dir . $file['name']);
        } elseif ($file['type'] === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            $content = $read->read_docx($dir . $file['name']);
        }

        $data = array(
            'post_title' => 'Post from document ' . $file['name'],
            'post_content' => $content
        );
        /**
         * Create post from file
         */
        $read->ws_insert($data);
        if (isset($read->post_id)) {
            /**
             * Log
             */
            $log->logWrite(array('file'=>$file['name'], 'post_id'=>$read->post_id));

            /**
             * Set meta data from file
             */
            $meta = array(
                'meta_title' => 'meta titolo ' . $file['name'],
                'meta_description' => 'meta description ' . $file['name']
            );
            $read->ws_update_meta($read->post_id, $meta);
            /**
             * Log
             */
            $log->logWrite(array('post_id'=>$read->post_id, 'meta'=>$meta));

            /**
             * Delete file
             */
            $file_c->ws_delete_file($dir . $file['name']);
            /**
             * Log
             */
            $log->logWrite("Deleted file ".$dir . $file['name']);

        } else {
            echo $read->error;
            $log->logWrite('Create Post ERROR'.$read->error);
        }

    }

}

