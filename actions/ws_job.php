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
$document = new ws_document();

$exHtmlResult = '';
$exHtmlResultError = '';
$contentError = '';
/**
 * Load file collection from working directory
 */
$files_collection = $file_c->ws_scandir($dir, $files_type_admitted);

/**
 * Create post for each document
 */
foreach($files_collection as $file){

    $content = null;
    $data = [];

    if(in_array($file['type'], $files_type_admitted)) {

        if ($file['type'] === 'application/msword') {
            $content = $read->read_doc($dir . $file['name']);
        } elseif ($file['type'] === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            $content = $read->read_docx($dir . $file['name']);
        }

        $dataBaseConf = [
            'post_type' => $postType,
            'post_status' => $postStatus
        ];

        if($docParsing == 1) {

            /**
             * Parse document
             */
            $file_c->ws_parse_file_content($content, $docSeparator, $docStructure);
            if (count($file_c->docContent) === 0) {
                $exHtmlResultError .= '<p><span class="orangeMsg">ERROR</span>: <b>' . $file['name'] . '</b> :: ' . $file_c->errorFile . '</p>';
                $log->logWrite("ERROR", array('file' => $file['name'], 'error' => $file_c->errorFile));
            } else {

                /**
                 * Content format
                 */
                $file_c->docContent['post_content'] = $document->ws_parse_document($file_c->docContent['post_content']);

                /**
                 * Set of data for WP post
                 * Get page parent ID
                 */
                if(strlen($postParent) > 0){
                    $docExtra = array(
                        'post_parent' => (int) get_post_parent_from_macroarea($file_c->docContent['acf_macroarea'], $postParent)
                    );
                    $data = array_merge($file_c->docContent, $docExtra);
                }else{
                    $data = $file_c->docContent;
                }


                /**
                 * Meta tags data
                 */
                $meta = array(
                    'meta_title' => $file_c->docContent['meta_title'],
                    'meta_description' => $file_c->docContent['meta_description'],
                    'focus_keyword' => isset($file_c->docContent['focus_keyword']) ? $file_c->docContent['focus_keyword'] : ''
                );

                /**
                 * ACF data
                 */
                $acfFields = ws_get_acf_from_config($acfMapping);

                /**
                 * Get Macoarea name if macroarea code exist
                 */
                $macroarea_code = $file_c->docContent['acf_macroarea'];
                if(preg_match("/([A-Z]+){1,4}/", $macroarea_code)){
                    $data['macroarea_name'] = wp_get_data_macroarea($macroarea_code)[0]->macroarea;
                    //Change name only if page is porto
                    if($data['acf_tipologia_pagina'] == 'porto'){
                        $data['acf_immagine'] = str_replace(".webp", '-'.strtolower($data['macroarea_name']).'.webp', $data['acf_immagine']);
                    }

                }

            }
        }

        /**
         * Check data and Create post
         */
        if(count($data)>0){
            $dataContent = $data ? array_merge($dataBaseConf, $data) : $dataBaseConf;


            /**
             * Create post from file
             */
            $read->ws_insert($dataContent);

            /**
             * Add extra fields to post
             */
            if (isset($read->post_id)) {
                /**
                 * Log
                 */
                $log->logWrite("INFO", array('file'=>$file['name'], 'post_id'=>$read->post_id));
                $exHtmlResult .= '<p>SUCCESS: '.$file['name'].' :: '.__('Created post ').'<a href="/wp-admin/post.php?post='.$read->post_id.'&action=edit" target="_blank">'.$read->post_id.'</a></p>';

                /**
                 * Set meta data from file
                 */
                if(isset($meta)){
                    $read->ws_update_meta($read->post_id, $meta);
                }

                /**
                 * Set ACF fields
                 */
                if(!empty($acfFields)){
                    //If exist contenuto_parte_1 and contenuto_parte_2
                    //Split content by h2
                    //Set each part of splitted content into contenuto_parte_1 and contenuto_parte_2
                    if($acfFields['contenuto_parte_1'] && $acfFields['contenuto_parte_2']){
                        $acfFieldsContent = [$acfFields['contenuto_parte_1'],$acfFields['contenuto_parte_2']];
                        $splittedContent = $read->split_content($acfFields, $data['post_content'], '<h2>', 2);
                        $data['contenuto_parte_1'] = str_replace("</p>>", "", preg_replace("/<\/p/", "</p>", $splittedContent['contenuto_parte_1']));
                        $data['contenuto_parte_2'] = str_replace("</p>>", "", preg_replace("/<\/p/", "</p>", $splittedContent['contenuto_parte_2']));
                    }

                    $read->ws_update_acf($read->post_id, $acfFields, $data);
                }

                /**
                 * Log
                 */
                $log->logWrite("INFO", array('post_id'=>$read->post_id, 'meta'=>$meta));
                $exHtmlResult .= '<p>SUCCESS: '.$file['name'].' :: '.__('Created meta tags for post ').$read->post_id.'</p>';

            } else {
                $log->logWrite("ERROR", 'Create Post ERROR'.$read->error);
                $exHtmlResultError .= '<p><span class="orangeMsg">ERROR</span>: '.__('Create post failed with error ').' :: '.$read->error.'</p>';
            }
        }


    }

    /**
     * Delete file
     */
    $file_c->ws_delete_file($dir . $file['name']);
    /**
     * Log
     */
    $log->logWrite("INFO", "Deleted file ".$dir . $file['name']);
    $exHtmlResult .= '<p>INFO: '.__('Deleted file ').$dir.$file['name'].'</p>';

}

$isError = strlen(trim($exHtmlResultError))>0 ? ' with error reported below. <a href="/wp-admin/admin.php?page=wp-import-word-log">Click here for reading the full log.</a>' : ' without error.';
$isErrorStyle = strlen(trim($exHtmlResultError))>0 ? 'orangeMsg' : 'greenMsg';
$contentError = '<p class="'.$isErrorStyle.'">Process terminated '.$isError.'</p>';
$contentError .= ($docAlertOnlyError == 1) ? $exHtmlResultError : $exHtmlResult.$exHtmlResultError;
/**
 * Print import log
 */
echo $contentError;

/**
 * Send email with import log to email contact
 */
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
        $log->debug_wpmail($sent_message, true);
    }
}