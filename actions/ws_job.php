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
$email = new ws_email();

$exHtmlResult = '';
$exHtmlResultError = '';
$contentError = '';
/**
 * Load file collection from working directory
 */
$files_collection = $file_c->ws_scandir($dir, $files_type_admitted);
if(count($files_collection) > 0){
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

            /**
             * You can import document's content with or without parsing it
             */
            if($docParsing == 1) {

                /**
                 * Document parsing
                 */
                $file_c->ws_parse_file_content($content, $docSeparator, $docStructure);
                if (count($file_c->docContent) === 0) {
                    $exHtmlResultError .= '<p><span class="orangeMsg">ERROR</span>: <b>' . $file['name'] . '</b> :: ' . $file_c->errorFile . '</p>';
                    $log->logWrite("ERROR", array('file' => $file['name'], 'error' => $file_c->errorFile));
                } else {
                    /**
                     * Content formatting
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
                     * Set tratta always active
                     * ACF filed has to be defined previous the import!
                     */
                    $data['tratta_status'] = 1;

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
                    $exHtmlResult .= '<p>SUCCESS: '.$file['name'].' :: '.__('Created post ','wpimportword').'<a href="/wp-admin/post.php?post='.$read->post_id.'&action=edit" target="_blank">'.$read->post_id.'</a></p>';

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
                    $exHtmlResult .= '<p>SUCCESS: '.$file['name'].' :: '.__('Created meta tags for post ','wpimportword').$read->post_id.'</p>';

                } else {
                    $log->logWrite("ERROR", 'Create Post ERROR'.$read->error);
                    $exHtmlResultError .= '<p><span class="orangeMsg">ERROR</span>: '.__('Create post failed with error ','wpimportword').' :: '.$read->error.'</p>';
                }
            }
        }else{
            $exHtmlResultError .= '<p><span class="orangeMsg">ERROR</span>: '.__('Wrong file type ','wpimportword').'</p>';
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
}else{
    $exHtmlResultError .= '<p><span class="orangeMsg">WARNING</span>: '.__('No files to load!','wpimportword').'</p>';
}

$isError = strlen(trim($exHtmlResultError))>0 ? __(' with error reported below. ','wpimportword').'<a href="/wp-admin/admin.php?page=wp-import-word-log">'.__('Click here for reading the full log.','wpimportword').'</a>' : __(' without error.','wpimportword');
$isErrorStyle = strlen(trim($exHtmlResultError))>0 ? __('orangeMsg','wpimportword') : __('greenMsg','wpimportword');
$contentError = '<p class="'.$isErrorStyle.'">'.__('Process terminated ','wpimportword').$isError.'</p>';
$contentError .= ($docAlertOnlyError == 1) ? $exHtmlResultError : $exHtmlResult.$exHtmlResultError;
/**
 * Print import log
 */
echo $contentError;

/**
 * Send email with import log to email contact
 */
if($docAlert == 1 && $validate->valid_email($docEmail) === TRUE){

    $ws_email_res = $email->ws_email_send($docEmail, $contentError);
    echo $ws_email_res['result_message'];
    $log->debug_wpmail($ws_email_res['sent_message'], true);

    mail('santi.walter@gmail.com','test','prova');
}