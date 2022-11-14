<?php

/**
 * Class for formatting content
 *
 */

class ws_document
{

    static string $h2_start = '[*';
    static string $h2_end = '*]';

    /**
     * Parse document e create a custom format
     * @param $content
     * @return array|string|string[]
     */
    public function ws_parse_document($content){
        $content = '<p>'.$content.'</p>';
        $content = str_replace(self::$h2_start, '</p><h2>', $content);
        $content = str_replace(self::$h2_end, '</h2><p>', $content);
        $content = str_replace('<br>', '</p></p>', $content);
        return $content;
    }

}