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
    public function ws_pare_document($content){
        $content = str_replace(self::$h2_start, '<h2>', $content);
        $content = str_replace(self::$h2_end, '</h2>', $content);
        return $content;
    }

}