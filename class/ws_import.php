<?php

class ws_import {

    public int $post_id;
    public string $error;

    /**
    * Read content from .docx & .doc Word document
    */
    public function read_docx($filename){

      $striped_content = '';
      $content = '';

      if(!$filename || !file_exists($filename)) return false;
      
      $zip = new ZipArchive();
      // Open the Microsoft Word .docx file as if it were a zip file... because it is.
      if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) return false;
      $zip->open($filename);

      // Fetch the document.xml file from the word subdirectory in the archive.
      $content = $zip->getFromName('word/document.xml');
      $zip->close();
      $content = str_replace('</w:r></w:p></w:tc><w:tc>', "<br> ", $content);
      $content = str_replace('</w:r></w:p>', "\r\n", $content);
      $striped_content = strip_tags($content);

      return $striped_content;

    }

    /**
     * Read content from .doc Word document
     */
    public function read_doc($userDoc){
        $fileHandle = fopen($userDoc, "r");
        $line = @fread($fileHandle, filesize($userDoc));
        $lines = explode(chr(0x0D),$line);
        $outtext = "";
        foreach($lines as $thisline)
        {
            $pos = strpos($thisline, chr(0x00));
            if (($pos !== FALSE)||(strlen($thisline)==0))
            {
            } else {
                $outtext .= $thisline." ";
            }
        }
        $outtext = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","",$outtext);
        return $outtext;
    }

    /**
     * Create WP post (type post or page)
     * @param $data
     * @return int|string|WP_Error
     */
    public function ws_insert($data){
    $my_post = array(
        'post_title'    => wp_strip_all_tags( $data['post_title'] ),
        'post_content'  => trim($data['post_content']),
        'post_status'   => $data['post_status'],
        'post_author'   => 1,
        #'guid' => $data['guid'],
        'post_type' => $data['post_type'],
        'post_parent' => $data['post_parent'] ?? 0,
        'post_name' => $data['slug'],
        'page_template' => wp_strip_all_tags($data['acf_tipologia_pagina']).'.php'
    );

      $this->post_id = wp_insert_post( $my_post );
      if(!is_wp_error($this->post_id)){
          return $this->post_id;
      }else{
          //there was an error in the post insertion,
          return $this->error = $this->post_id->get_error_message();
      }

    }

    /**
     * See https://www.wpallimport.com/documentation/yoast-wordpress-seo/
     * @param $postid
     * @param $meta
     * @return void
     */
    public function ws_update_meta($postid, $meta){
        if(!empty($meta)){
            update_post_meta( $postid, '_yoast_wpseo_title', $meta['meta_title'] );
            update_post_meta( $postid, '_yoast_wpseo_metadesc', $meta['meta_description'] );
            update_post_meta( $postid, '_yoast_wpseo_focuskw', $meta['focus_keyword'] );
            update_post_meta( $postid, '_yoast_wpseo_canonical', (isset($meta['slug']) ?? ''));
        }
    }

    /**
     * Update ACF field for post specified by id
     * @param $postid
     * @param $acfFields
     * @param $data
     * @return void
     */
    public function ws_update_acf($postid, $acfFields, $data){
        if(!empty($acfFields)){
            foreach($acfFields as $scfKey => $acfValue){
                update_field($acfValue, $data[$scfKey], $postid);
            }
        }
    }

    /**
     * Extract exact position of a string
     * @param $haystack
     * @param $needle
     * @param $number
     * @return false|int
     */
    public function strposX($haystack, $needle, $number = 0)
    {
        return strpos($haystack, $needle,
            $number > 1 ?
                $this->strposX($haystack, $needle, $number - 1) + strlen($needle) : 0
        );
    }

    /**
     * Split content in "number_of_occurrence" occurrences
     * @param $acfFields
     * @param $content
     * @param $needle
     * @param $number_of_occurrence
     * @return array
     */
    public function split_content($acfFields, $content, $needle, $number_of_occurrence){
        $acfFields_res = [];
        if($acfFields['contenuto_parte_1'] && $acfFields['contenuto_parte_2']){
            $h2pos = $this->strposX($content, $needle, $number_of_occurrence);
            $acfFields_res = [
                        'contenuto_parte_1' => substr($content, 0, $h2pos-1),
                        'contenuto_parte_2' => substr($content, $h2pos, strlen($content))
            ];
        }
        return $acfFields_res;
    }

}
