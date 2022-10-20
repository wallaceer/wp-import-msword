<?php

class ws_import {

  /**
  * Read content from Word document
  */
  public function read_docx($filename){

      $striped_content = '';
      $content = '';

      if(!$filename || !file_exists($filename)) return false;

      $zip = zip_open($filename);
      if (!$zip || is_numeric($zip)) return false;

      while ($zip_entry = zip_read($zip)) {

          if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

          if (zip_entry_name($zip_entry) != "word/document.xml") continue;

          $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

          zip_entry_close($zip_entry);
      }
      zip_close($zip);
      $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
      $content = str_replace('</w:r></w:p>', "\r\n", $content);
      $striped_content = strip_tags($content);

      return $striped_content;
  }


  public function ws_insert($data){
    $my_post = array(
        'post_title'    => wp_strip_all_tags( $data['post_title'] ),
        'post_content'  => $data['post_content'],
        'post_status'   => 'publish',
        'post_author'   => 1
    );

    // Insert the post into the database
    return wp_insert_post( $my_post );
  }


  public function ws_update_meta($postid, $meta){
    update_post_meta( $postid, '_yoast_wpseo_title', $meta['meta_title'] );
    update_post_meta( $postid, '_yoast_wpseo_metadesc', $meta['meta_description'] );
  }

    /**
     * Empty the log file
     */
    public function emptyFileLog(){
        $file = static::$logfile;
        $f = fopen($file, "r+");
        if ($f !== false) {
            ftruncate($f, 0);
            fclose($f);
        }
    }

}
