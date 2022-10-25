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

        /**
         * Parse document
         */
        $file_c->ws_parse_file_content($content, $docSeparator, $docStructure);
        if(count($file_c->docContent) === 0){
            return $log->logWrite("ERROR", array('file'=>$file['name'], 'error'=>$file_c->errorFile));
        }

        $docExtra = array(
            'post_status' => $postStatus
        );

        /**
         * Set of data for WP post
         */
        $data = array_merge($file_c->docContent, $docExtra);

        /**
         * Create post from file
         */
        $read->ws_insert($data);
        if (isset($read->post_id)) {
            /**
             * Log
             */
            $log->logWrite("INFO", array('file'=>$file['name'], 'post_id'=>$read->post_id));

            /**
             * Set meta data from file
             */
            $meta = array(
                'meta_title' => $file_c->docContent['meta_title'],
                'meta_description' => $file_c->docContent['meta_description'],
                'focus_keyword' => $file_c->docContent['focus_keyword']
            );
            $read->ws_update_meta($read->post_id, $meta);
            /**
             * Log
             */
            $log->logWrite("INFO", array('post_id'=>$read->post_id, 'meta'=>$meta));

            /**
             * Delete file
             */
            $file_c->ws_delete_file($dir . $file['name']);
            /**
             * Log
             */
            $log->logWrite("INFO", "Deleted file ".$dir . $file['name']);

            return true;

        } else {
            $log->logWrite("ERROR", 'Create Post ERROR'.$read->error);
            return $read->error;
        }

    }

}

