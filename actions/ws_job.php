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
$validate = new ws_validate();

$exHtmlResult = '';
$exHtmlResultError = '';
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
            $exHtmlResultError .= '<p>ERROR: '.$file['name'].' :: '.$file_c->errorFile.'</p>';
            $log->logWrite("ERROR", array('file'=>$file['name'], 'error'=>$file_c->errorFile));
        }else{

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
                $exHtmlResult .= '<p>SUCCESS: '.$file['name'].' :: '.__('Created post ').$read->post_id.'</p>';

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
                $exHtmlResult .= '<p>SUCCESS: '.$file['name'].' :: '.__('Created meta tags for post ').$read->post_id.'</p>';

                /**
                 * Delete file
                 */
                $file_c->ws_delete_file($dir . $file['name']);
                /**
                 * Log
                 */
                $log->logWrite("INFO", "Deleted file ".$dir . $file['name']);
                $exHtmlResult .= '<p>INFO: '.__('Deleted file ').$dir.$file['name'].'</p>';

            } else {
                $log->logWrite("ERROR", 'Create Post ERROR'.$read->error);
                $exHtmlResultError .= '<p>ERROR: '.__('Create post failed with error ').' :: '.$read->error.'</p>';
            }

        }

    }

}

/**
 * Send email with import log to emai contact
 */
$contentError = ($docAlertOnlyError == 1) ? $exHtmlResultError : $exHtmlResult.$exHtmlResultError;
if($docAlert == 1 && $validate->valid_email($docEmail) === TRUE){
    $siteFromName = get_bloginfo( 'name' );
    $siteFromEmail = get_bloginfo( 'admin_email' );
    $headers = [
        "Content-Type: text/html; charset=UTF-8",
        "From: $siteFromName <$siteFromEmail>",
        "Cc: $siteFromEmail"
    ];
    $sent_message = wp_mail($docEmail, 'WP Import from Word Log', $contentError, $headers);
    if ( $sent_message ) {
        // The message was sent.
        echo 'The test message was sent. Check your email inbox.';
    } else {
        // The message was not sent.
        echo 'The message was not sent!';
        $log->debug_wpmail($sent_message);
    }
}
/**
 * Print import log
 */
echo $contentError;